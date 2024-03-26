<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

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
        if(!array_key_exists("saved",$data) || is_null($data["saved"])) return null;
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
    public function canBeRequired():bool {
        return true;
    }


    // null means that it isn't overwritten
    public function overwrittenRules():?array { //ToDo
        return null;
    }

    // null means that it isn't overwritten
    public function overwrittenAnchorRules():?array { //ToDo
        return null;
    }

}
