<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;

abstract class FieldRuleAnchorType
{

    /*
     * Static Class Functions
     */
    public static function getAllAnchors():array{
        $output = [];
        foreach(config("ffhs_custom_forms.field_rule_anchor_types") as $typeClass)
            $output[$typeClass::identifier()]= $typeClass;
        return $output;
    }
    public static function getTypeClassFromName(string $typeName): ?string {
        $types = self::getAllAnchors();
        if(!array_key_exists($typeName,$types)) return null;
        return self::getAllAnchors()[$typeName];
    }

    public static function getAnchorFromName(string $typeName): ?FieldRuleAnchorType {
        $class = self::getTypeClassFromName($typeName);
        if(is_null($class)) return null;
        return new $class();
    }


    public static abstract function identifier();


    public abstract function shouldRuleExecute(array $formState, CustomField $customField, FieldRule $rule):bool;

    public function canRuleExecute(Component $component, CustomField $customField, FieldRule $rule ):bool {
        $rawFormData = array_values($component->getLivewire()->getCachedForms())[0]->getRawState();
        return $this->shouldRuleExecute($rawFormData,$customField,$rule);
    }

    public abstract function settingsComponent(CustomForm $customForm, array $fieldData):Component;



    public function getTranslatedName(): string {
        return __("custom_forms.anchors" . self::identifier());
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return true;
    }
    public function mutateRenderParameter(array $parameter, CustomField $customField, FieldRule $rule): array {
        return $parameter;
    }
    public function mutateDataBeforeLoadInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }

    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }



}
