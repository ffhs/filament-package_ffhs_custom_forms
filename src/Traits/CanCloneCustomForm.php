<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\FormRuleEventType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;

trait CanCloneCustomForm
{
    //ToDo Test and maby improve
    public function cloneField(array $fieldData, CustomForm $targetForm, bool $useSameIdentifier = true): array
    {
        //Mutate Field Data's
        $field = CustomField::query()->firstWhere('id', $fieldData['id']);

        //Load OptionData now, because it needs the field id

        $fieldData = $field->getType()->mutateOnCloneField($fieldData, $field);
        $fieldData = $this->mutateOptionData($fieldData, $field);

        $fieldData = $this->unsetAttributesForClone($fieldData);
        $fieldData['custom_form_id'] = $targetForm->id;

        return $fieldData;
    }

    protected function mutateOptionData(array $fieldData, CustomField $original): array
    {
        $type = $original->getType();
        $typeOptions = $type->getFlattenExtraTypeOptions();
        $optionData = $fieldData['options'];

        foreach ($typeOptions as $optionKey => $typeOption) {
            /**@var TypeOption $typeOption */
            $optionData[$optionKey] = $typeOption->mutateOnFieldClone($optionData[$optionKey], $optionKey, $original);
        }

        $fieldData['options'] = $optionData;
        return $fieldData;
    }

    protected function cloneRule(array $ruleData, CustomForm $targetForm): array
    {
        $ruleData = $this->unsetAttributesForClone($ruleData);
        unset($ruleData['pivot']);

        $ruleData['events'] = $this->cloneRuleComponents($ruleData['events'], $targetForm, FormRuleEventType::class);
        $ruleData['triggers'] = $this->cloneRuleComponents($ruleData['triggers'], $targetForm,
            FormRuleTriggerType::class);

        return $ruleData;
    }

    protected function cloneRuleComponents(
        array $ruleComponentsData,
        CustomForm $targetForm,
        string|EventType|TriggerType $componentClass
    ): array {
        foreach ($ruleComponentsData as $eventKey => $data) {
            $data = $this->unsetAttributesForClone($data);

            $type = $data['type'];
            $type = $componentClass::getTypeFromIdentifier($type);

            if ($type instanceof FormRuleEventType || $type instanceof FormRuleTriggerType) {
                $data = $type->mutateDataOnClone($data, $targetForm);
            }

            unset($data['rule_id']);
            $ruleComponentsData[$eventKey] = $data;
        }

        return $ruleComponentsData;
    }

    private function unsetAttributesForClone(array $data): array
    {
        unset($data['id'], $data['created_at'], $data['deleted_at'], $data['updated_at']);
        return $data;
    }
}
