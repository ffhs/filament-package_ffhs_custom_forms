<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\EditHelper;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Events\FormRuleEventType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;


class EditCustomFormCloneHelper
{

    public static function cloneField(array $fieldData, CustomForm $targetForm, bool $useSameIdentifier = true):array {

        //Mutate Field Data's
        $field = CustomField::cached($fieldData["id"]);

        //Load OptionData now, because it needs the field id

        $fieldData = $field->getType()->mutateOnCloneField($fieldData, $field);
        $fieldData = static::mutateOptionData($fieldData, $field);

        $fieldData = static::unsetAttributesForClone($fieldData);
        $fieldData["custom_form_id"] = $targetForm->id;

        return $fieldData;
    }

    public static function mutateOptionData(array $fieldData, CustomField $original):array
    {
        $type = $original->getType();
        $typeOptions = $type->getFlattenExtraTypeOptions();
        $optionData = $fieldData["options"];

        foreach ($typeOptions as $optionKey => $typeOption) {
            /**@var TypeOption $typeOption*/
            $optionData[$optionKey] = $typeOption->mutateOnFieldClone($optionData[$optionKey], $optionKey, $original);
        }

        $fieldData["options"] = $optionData;
        return $fieldData;
    }

    private static function unsetAttributesForClone(array $data):array {
        unset($data["id"]);
        unset($data["created_at"]);
        unset($data["deleted_at"]);
        unset($data["updated_at"]);
        return $data;
    }


    public static function cloneRule(array $ruleData, CustomForm $targetForm):array
    {

        $ruleData = static::unsetAttributesForClone($ruleData);
        unset($ruleData["pivot"]);

        $ruleData["events"] = static::cloneRuleComponents($ruleData["events"], $targetForm, FormRuleEventType::class);
        $ruleData["triggers"] = static::cloneRuleComponents($ruleData["triggers"], $targetForm, FormRuleTriggerType::class);

        return $ruleData;
    }


    public static function cloneRuleComponents(
        array $ruleComponentsDatas,
        CustomForm $targetForm,
        string|EventType|TriggerType $componentClass
    ):array
    {
        foreach ($ruleComponentsDatas as $eventKey => $data) {
            $data = static::unsetAttributesForClone($data);

            $type = $data["type"];
            $type= $componentClass::getTypeFromIdentifier($type);

            if($type instanceof FormRuleEventType || $type instanceof FormRuleTriggerType){
                $data = $type->mutateDataOnClone($data, $targetForm);
            }

            unset($data["rule_id"]);
            $ruleComponentsDatas[$eventKey] = $data;
        }

        return $ruleComponentsDatas;
    }

}
