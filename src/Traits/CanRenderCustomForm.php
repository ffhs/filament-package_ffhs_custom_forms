<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRule;
use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\ChildFieldRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
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
        $rules = collect($customForm->getRules());

        if ($customFields->isEmpty()) {
            return [[], collect()];
        }

        //Rule before render
        $this->runRulesBeforeRender($rules, $customFields, $displayer->getRuleActionBeforeRender());

        //Rule Rendering
        /**@var Collection $allComponents */
        [$customFormSchema, $allComponents] = $this->renderCustomFormRaw(
            $viewMode,
            $displayer,
            $customForm,
            $customFields,
            $positionOffset
        );

        //TriggerPreparation (maintain for live attribute)
        $this->runRulesAfterRender($rules, $allComponents, $customForm, $customFields,
            $displayer->getRuleActionAfterRender());

        return [$customFormSchema, $allComponents];
    }

    public function renderCustomFormRaw(
        string $viewMode,
        FieldDisplayer $displayer,
        EmbedCustomForm $customForm,
        Collection $customFields,
        int $positionOffset
    ): array {
        $customFields = $customFields
            ->mapWithKeys(fn(EmbedCustomField $embedField) => [$embedField->getFormPosition() => $embedField]);
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
            if (is_null($customField) || !$customField->isActive()) {
                continue;
            }

            //if a field is a layout field, add Render Components
            if (!is_null($customField->getLayoutEndPosition())) {
                $fieldRenderData = $customFields
                    ->filter(function (EmbedCustomField $field) use ($customField) {
                        return $field->getFormPosition() > $customField->getFormPosition() &&
                            $field->getFormPosition() <= $customField->getLayoutEndPosition();
                    });

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

            $allComponents[$customField->identifier()] = $renderedComponent;
            $customFormSchema[] = $renderedComponent;
        }

        return [$customFormSchema, $allComponents];
    }

    protected function runRulesBeforeRender(Collection $rules, Collection &$customFields, FormRuleAction $action): void
    {
        $rules->each(function (Rule $rule) use ($action, &$customFields) {
            $customFields = $rule->handle($action, [], $customFields);
        });
    }

    protected function runRulesAfterRender(
        Collection $rules,
        array &$allComponents,
        EmbedCustomForm $customForm,
        Collection &$customFields,
        FormRuleAction $action
    ): void {
        $rules
            ->map(fn(EmbedRule $rule) => $rule->getTriggers())
            ->flatten(1)
            ->filter(fn(RuleTrigger $ruleTrigger) => !is_null($ruleTrigger))
            ->filter(fn(RuleTrigger $ruleTrigger) => $ruleTrigger->getType() instanceof FormRuleTriggerType)
            ->each(function (RuleTrigger $trigger) use (&$allComponents) {
                /** @phpstan-ignore-next-line */
                $allComponents = $trigger->getType()->prepareComponents($allComponents, $trigger);
            });

        $customFields = $customForm
            ->getCustomFields()
            ->mapWithKeys(fn(CustomField $field) => [$field->identifier => $field]);

        $rules->each(function (Rule $rule) use ($action, $customFields, &$allComponents) {
            $data = ['custom_fields' => $customFields];
            $allComponents = $rule->handle($action, $data, $allComponents);
        });
    }
}
