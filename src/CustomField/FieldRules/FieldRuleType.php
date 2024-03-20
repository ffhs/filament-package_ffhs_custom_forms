<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;

abstract class FieldRuleType
{

    /*
     * Static Class Functions
     */
    public static function getAllAnchors():array{
        $output = [];
        foreach(config("ffhs_custom_forms.field_rule_types") as $typeClass)
            $output[$typeClass::identifier()]= $typeClass;
        return $output;
    }
    public static function getTypeClassFromName(string $typeName): ?string {
        $types = self::getAllAnchors();
        if(!array_key_exists($typeName,$types)) return null;
        return self::getAllAnchors()[$typeName];
    }

    public static function getRuleFromName(string $typeName): ?FieldRuleType {
        $class = self::getTypeClassFromName($typeName);
        if(is_null($class)) return null;
        return new $class();
    }


    public abstract function identifier();
    public function canAddOnField(CustomFieldType $type) {
        return true;
    }

    public function mutateDataBeforeRuleLoadInEdit(array $data, FieldRule $rule) {

    }


    public function mutateDataBeforeRuleSaveInEdit(array $data, FieldRule $rule) {

    }

    public function runBeforeRender(FieldRule $rule, CustomFieldAnswer $answer):void {

    }
    public function afterRender(Component $component, FieldRule $rule, CustomFieldAnswer $answer): Component {
        return $component;
    }

    public function mutateLoadeAnswerData(array $answerData, FieldRule $rule, CustomFieldAnswer $answer):array {
        return $answerData;
    }
    public function mutateSaveAnswere(array $answerData, FieldRule $rule, CustomFieldAnswer $answer):array  {
        return $answerData;
    }

    public function afterAnswereSave(FieldRule $rule, CustomFieldAnswer $answer):void {

    }

}
