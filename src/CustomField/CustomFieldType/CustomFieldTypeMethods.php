<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

abstract class CustomFieldTypeMethods
{


    public static abstract function getFieldIdentifier():String;
    public abstract function viewModes():array;
    public abstract function icon():String;



    public function prepareSaveFieldData(mixed $data): ?array{
        if(is_null($data)) return null;
        return ["saved"=> $data];
    }
    public function prepareLoadFieldData(array $data): mixed{
        if(empty($data["saved"])) return null;
        return $data["saved"];
    }

    public function getTranslatedName():String{
        return __("custom_forms.types." . self::getFieldIdentifier());
    }

    public function getExtraTypeOptions():array{
        return [];
    }
    public function getExtraGeneralTypeOptions():array{
        return [];
    }

    //ToDo Mutate answerers (Save,  Create)
    public function canBeDeactivate():bool {
        return true;
    }

    // null means that it isn't overwritten
    public function overwrittenRules():?array {
        return null;
    }

    public function canBeRequired():bool {
        return true;
    }


}
