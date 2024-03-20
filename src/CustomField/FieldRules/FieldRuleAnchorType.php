<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
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

    public abstract function shouldRuleExecute(CustomFormAnswer $formAnswer, CustomFieldAnswer $fieldAnswer, FieldRule $rule):bool;

    public abstract function createComponent(CustomForm $customForm, array $fieldData):Component;

    public function mutateAnchorSave(array $data, CustomField $field, FieldRule $rule):array{
        return $data;
    }

    public function getTranslatedName() {
        //toDo
        return $this->identifier();
    }

}
