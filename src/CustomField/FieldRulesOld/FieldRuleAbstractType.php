<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Infolists\Components\Component as InfoComponent;
use Illuminate\Support\Collection;

abstract class FieldRuleAbstractType
{
    use IsType;


    /*
     * Static Class Functions
     */
    /*public static function getAllAnchors():array{
        $output = [];
        foreach(config("ffhs_custom_forms.field_rule_types") as $typeClass)
            $output[$typeClass::identifier()]= $typeClass;
        return $output;
    }
    public static function getTypeClassFromIdentifier(string $typeName): ?string {
        $types = self::getAllAnchors();
        if(!array_key_exists($typeName,$types)) return null;
        return self::getAllAnchors()[$typeName];
    }

    public static function getTypeFromIdentifier(string $typeName): ?FieldRuleType {
        $class = self::getTypeClassFromIdentifier($typeName);
        if(is_null($class)) return null;
        return new $class();
    }*/
    public static function getConfigTypeList():string {
        return "field_rule_types";
    }


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



    public function beforeComponentRender(FieldRule $rule):void {

    }
    public function mutateRenderParameter(array $parameter, FieldRule $rule): array {
        return $parameter;
    }
    public function afterComponentRender(Component|InfoComponent $component, FieldRule $rule): Component|InfoComponent  {
        return $component;
    }

    public function afterAllFormComponentsRendered(FieldRule $rule, Collection $components):void {

    }

    public function mutateLoadAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed {
        return $answerData;
    }
    public function mutateSaveAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed  {
        return $answerData;
    }

    public function afterAnswerSave( FieldRule $rule, CustomFieldAnswer $answer):void {

    }

    public function canRuleExecute(Component|InfoComponent $component, FieldRule $rule):bool {
        return $rule->getAnchorType()->canRuleExecute($component,$rule);
    }

    public function getTranslatedName():string {
        return __("custom_forms.rules." . $this::identifier());
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string {
        return $this->getTranslatedName();
    }

    public function mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):array {
        unset($data["id"]);
        return $data;
    }

}
