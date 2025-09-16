<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\ChildFieldRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Collection;

trait CanRenderCustomForm
{
    public function generateFormSchema(EmbedCustomForm $form, string $viewMode): array
    {
        $columns = $form->getFormConfiguration()->getColumns();
        $customFields = $form->getOwnedFields();

        $render = FormFieldDisplayer::make($form);
        $renderOutput = $this->renderCustomForm($viewMode, $render, $form, $customFields);

        return [
            Group::make($renderOutput[0])->columns($columns),
        ];
    }

    public function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode): array
    {
        $form = $formAnswer->customForm;
        $customFields = $form->getOwnedFields();
        $columns = $form->getFormConfiguration()->getColumns();

        $render = EntryFieldDisplayer::make($formAnswer);
        $renderOutput = $this->renderCustomForm($viewMode, $render, $form, $customFields);

        return [
            Group::make($renderOutput[0])
                ->columns($columns),
        ];
    }

    public function renderCustomForm(
        string $viewMode,
        FieldDisplayer $displayer,
        EmbedCustomForm $customForm,
        Collection $customFields,
        int $positionOffset = 0,
    ): array {
        $rules = collect([]);//ToDo $customForm->rules;

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
        EmbedCustomForm $customForm,
        Collection $customFields,
        int $positionOffset
    ): array {
        $customFields = $customFields->mapWithKeys(fn(EmbedCustomField $embedField
        ) => [$embedField->form_position => $embedField]);
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
            'child_render' => fn() => []
        ];

        for (
            $formPosition = $positionOffset + 1;
            $formPosition <= $customFields->count() + $positionOffset;
            $formPosition++
        ) {
            /** @var EmbedCustomField $customField */
            $customField = $customFields->get($formPosition);
            $parameters = $defaultParameters;

            //When field isn't Active skip it or not exist
            if (is_null($customField) || !$customField->is_active) {
                continue;
            }

            //if field is a layout field, add Render Components
            if (!is_null($customField->layout_end_position)) {
                $fieldRenderData = $customFields
                    ->filter(function (EmbedCustomField $field) use ($customField) {
                        return $field->form_position > $customField->form_position &&
                            $field->form_position <= $customField->layout_end_position;
                    });
//                    ->where('form_position', '>', $customField->form_position)
//                    ->where('form_position', '<=', $customField->layout_end_position);

                $parameters['child_fields'] = $fieldRenderData;
                $parameters['child_render'] = ChildFieldRender::make(
                    $viewMode,
                    $displayer,
                    $customForm,
                    $fieldRenderData,
                    $registerRenderedComponents,
                    $formPosition
                );

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
        EmbedCustomForm $customForm,
        Collection &$customFields,
    ): void {
        return; //toDo repair
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
        });
    }
}
