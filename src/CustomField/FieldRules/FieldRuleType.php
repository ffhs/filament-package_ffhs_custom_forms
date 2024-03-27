<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfoComponent;

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


    public abstract static function identifier();
    public abstract function settingsComponent(CustomForm $customForm, array $fieldData):Component;
    public abstract function getCreateRuleData():array; //ToDo I think it is possible to replace something in the Formmodal of the action to load the default values


    public function canAddOnField(CustomFieldType $type): bool {
        return true;
    }


    public function mutateDataBeforeLoadInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }

    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }




    public function beforeRender(CustomField $customField, FieldRule $rule):void {

    }
    public function mutateRenderParameter(array $parameter, CustomField $customField, FieldRule $rule): array {
        return $parameter;
    }
    public function afterRender(Component|InfoComponent $component, CustomField $customField, FieldRule $rule): Component|InfoComponent  {
        return $component;
    }

    public function mutateLoadAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed {
        return $answerData;
    }
    public function mutateSaveAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed  {
        return $answerData;
    }

    public function afterAnswerSave( FieldRule $rule, CustomFieldAnswer $answer):void {

    }

    public function getTranslatedName():string {
        return __("custom_forms.rules." . self::identifier());
    }

}
