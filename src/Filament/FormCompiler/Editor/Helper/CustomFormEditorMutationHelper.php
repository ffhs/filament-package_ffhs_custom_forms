<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;

class CustomFormEditorMutationHelper
{
/*
 *     public static function mutateRuleDataOnLoad(array $data, CustomForm $customForm): array {
        $data["rules"] = [];
        *@var CustomField $customField
$customField = $customForm->customFields->where("id",$data["id"])->first();
if(is_null($customField)) return $data;
foreach ($customField->fieldRules as $rule){
    /**@var FieldRule $rule*
$ruleData = $rule->toArray();
$ruleData = $rule->getRuleType()->mutateDataBeforeLoadInEdit($ruleData,$rule) ;
$ruleData =   $rule->getAnchorType()->mutateDataBeforeLoadInEdit($ruleData,$rule);
$data["rules"][] = $ruleData;
}

return $data;
}

public static function mutateOptionData(array $data, CustomForm $customForm): array {
    if(!array_key_exists("options",$data) || is_null($data["options"])) $data["options"] = [];

    $type = CustomFieldUtils::getFieldTypeFromRawDate($data);
    $field = $customForm->customFields->where("id",$data["id"])->first();
    if($field == null) return $data;


    foreach ($type->getExtraTypeOptions() as $name => $option){
        /**@var TypeOption $option*
        if(!array_key_exists($name, $data["options"])) $data["options"][$name] = $option->mutateOnLoad(null, $field);
        else $data["options"][$name] = $option->mutateOnLoad($data["options"][$name],$field);
    }

    return $data;
}
 */
}
