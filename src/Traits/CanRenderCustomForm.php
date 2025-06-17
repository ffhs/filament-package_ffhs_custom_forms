<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\ChildFieldRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\InfolistFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Infolists\Components\Group;
use Illuminate\Support\Collection;

trait CanRenderCustomForm
{
    public function generateFormSchema(CustomForm $form, string $viewMode): array
    {
        $customFields = $form->getOwnedFields();

        $render = FormFieldDisplayer::make($form);
        $renderOutput = $this->renderCustomForm($viewMode, $render, $form, $customFields);

        //ToDo Maby add default con to FormConfiguration
        return [
            \Filament\Forms\Components\Group::make($renderOutput[0])->columns(config('ffhs_custom_forms.default_column_count')),
        ];
    }

    public function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode): array
    {
        $form = $formAnswer->customForm;
        $customFields = $form->getOwnedFields();
        $fieldAnswers = $formAnswer->customFieldAnswers;


        $render = InfolistFieldDisplayer::make($formAnswer, $fieldAnswers);
        $renderOutput = $this->renderCustomForm($viewMode, $render, $form, $customFields);;

        //ToDo Maby add default con to FormConfiguration
        return [
            Group::make($renderOutput[0])->columns(config('ffhs_custom_forms.default_column_count')),
        ];
    }

    public function renderCustomForm(
        string $viewMode,
        FieldDisplayer $displayer,
        CustomForm $customForm,
        Collection $customFields,
        int $positionOffset = 0,
    ): array {
        $rules = $customForm->rules;

        if ($customFields->isEmpty()) {
            return [[], collect()];
        }

        //Rule before render
        $this->runRulesBeforeRender($rules, $customFields);

        //Rule Rendering
        /**@var Collection $allComponents */
        [$customFormSchema, $allComponents] = $this->renderCustomFormRaw(
            $viewMode,
            $displayer,
            $customForm,
            $customFields,
            $positionOffset
        );

        //TriggerPreparation (maintain for live attribute)  ca 10ms
        $this->runRulesAfterRender($rules, $allComponents, $customForm, $customFields);

        return [$customFormSchema, $allComponents];
    }

    public function renderCustomFormRaw(
        string $viewMode,
        FieldDisplayer $displayer,
        CustomForm $customForm,
        Collection $customFields,
        int $positionOffset
    ): array {
        $customFields = $customFields->keyBy('form_position');
        $customFormSchema = [];
        $allComponents = [];
        //This Function allows to register the rendered components to $allComponents for the rules
        $registerRenderedComponents = static function (array $components) use (&$allComponents) {
            $allComponents += $components;
        };
        $defaultParameters = [
            'viewMode' => $viewMode,
            'registerComponents' => $registerRenderedComponents,
            'displayer' => $displayer,
        ];


        for ($formPosition = $positionOffset + 1; $formPosition <= $customFields->count() + $positionOffset; $formPosition++) {
            /** @var CustomField $customField */
            $customField = $customFields->get($formPosition);
            $parameters = $defaultParameters;

            //When field isn't Active skip it or not exist
            if (is_null($customField) || !$customField->is_active) {
                continue;
            }

            //if field is a layout field, add Render Components
            if (!is_null($customField->layout_end_position)) {
                $fieldRenderData = $customFields
                    ->where('form_position', '>', $customField->form_position)
                    ->where('form_position', '<=', $customField->layout_end_position);

                $parameters = [
                    ...$parameters,
                    'child_fields' => $fieldRenderData,
                    //Render Schema Input
                    'child_render' => ChildFieldRender::make(
                        $viewMode,
                        $displayer,
                        $customForm,
                        $customFields,
                        $registerRenderedComponents,
                        $formPosition
                    )
                ];
                //Skip fields where in the sub renderer Index
                $formPosition += $fieldRenderData->count();
            }

            //render Field
            $renderedComponent = $displayer($viewMode, $customField, $parameters);

            $allComponents[$customField->identifier] = $renderedComponent;
            $customFormSchema[] = $renderedComponent;
        }

        return [$customFormSchema, $allComponents];
    }

    protected function runRulesBeforeRender(Collection $rules, Collection &$customFields): void
    {
        $rules->each(function (Rule $rule) use (&$customFields) {
            $customFields = $rule->handle(['action' => 'before_render'], $customFields);
        });
    }

    protected function runRulesAfterRender(
        Collection $rules,
        array &$allComponents,
        CustomForm $customForm,
        Collection &$customFields,
    ): void {

        $rules
            ->map(fn(Rule $rule) => $rule->ruleTriggers)
            ->flatten(1)
            ->filter(fn(RuleTrigger $ruleTrigger) => !is_null($ruleTrigger))
            ->filter(fn(RuleTrigger $ruleTrigger) => $ruleTrigger->getType() instanceof FormRuleTriggerType)
            ->each(function (RuleTrigger $trigger) use (&$allComponents) {
                $allComponents = $trigger->getType()->prepareComponents($allComponents, $trigger);
            });

        $customFields = $customForm
            ->customFields
            ->mapWithKeys(fn(CustomField $field) => [$field->identifier => $field]);


        $rules->each(function (Rule $rule) use ($customFields, &$allComponents) {

            $data = ['action' => 'after_render', 'custom_fields' => $customFields];
            $allComponents = $rule->handle($data, $allComponents);
//            dump('----------------------------------------------------------------');
//            dump($allComponents);
//            dump($data);
//            dump('----------------------------------------------------------------');
        });
    }
}
