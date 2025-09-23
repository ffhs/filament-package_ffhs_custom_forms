<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Contracts\Rules\EventType;
use Ffhs\FfhsUtils\Contracts\Rules\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\FormRuleEventType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFieldDataContainer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;

trait CanCloneCustomForm
{
    public function cloneField(array $fieldData, CustomFormConfiguration $configuration): array
    {
        //Mutate Field Data's
        CustomFieldDataContainer::make($fieldData, $configuration);
        $field = CustomField::query()
            ->firstWhere('id', $fieldData['id']);

        //Load OptionData now, because it needs the field id

        $fieldData = $field
            ->getType()
            ->mutateOnCloneField($fieldData, $field);
        $fieldData = $this->mutateOptionData($fieldData, $field);
        $fieldData = $this->unsetAttributesForClone($fieldData);

        unset($fieldData['custom_form_id']);

        return $fieldData;
    }

    protected function mutateOptionData(array $fieldData, CustomField $original): array
    {
        $type = $original->getType();
        $typeOptions = $type->getFlattenExtraTypeOptions();
        $optionData = $fieldData['options'];

        foreach ($typeOptions as $optionKey => $typeOption) {
            /**@var TypeOption $typeOption */
            $optionData[$optionKey] = $typeOption
                ->mutateOnFieldClone($optionData[$optionKey], $optionKey, $original);
        }

        $fieldData['options'] = $optionData;

        return $fieldData;
    }

    protected function cloneRule(array $ruleData): array
    {
        $ruleData = $this->unsetAttributesForClone($ruleData);

        unset($ruleData['pivot']);

        $ruleData['events'] = $this
            ->cloneRuleComponents($ruleData['events'], FormRuleEventType::class);
        $ruleData['triggers'] = $this
            ->cloneRuleComponents($ruleData['triggers'], FormRuleTriggerType::class);

        return $ruleData;
    }

    protected function cloneRuleComponents(
        array $ruleComponentsData,
        string|EventType|TriggerType $componentClass
    ): array {
        foreach ($ruleComponentsData as $eventKey => $data) {
            $data = $this->unsetAttributesForClone($data);

            $type = $data['type'];
            $type = $componentClass::getTypeFromIdentifier($type);

            if ($type instanceof FormRuleEventType || $type instanceof FormRuleTriggerType) {
                $data = $type->mutateDataOnClone($data);
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
