<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Illuminate\Support\Collection;

class FieldMapper
{
    public static function getIdentifyKey(CustomField|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customField;
        return  $record->identifier;
    }

    public static function getLabelName(CustomField|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customField;
        $label = $record->name;
        if($label != "") $label .= ":";
        return $label;
    }

    public static function getOptionParameter(CustomField|CustomFieldAnswer $record, string $option, bool $canBeNull = false):mixed{
        if($record instanceof CustomFieldAnswer) $record=  $record->customField;
        if(is_null($record->options)) $record->options = [];
        if(array_key_exists($option, $record->options) ){
            $return = $record->options[$option];
            if(!is_null($return)) return $return;
            else if($canBeNull) return null;
        }


        $generalOptions = $record->getType()->getDefaultGeneralOptionValues();
        if(array_key_exists($option, $generalOptions)) return $generalOptions[$option];
        $fieldOptions = $record->getType()->getDefaultTypeOptionValues();
        if(array_key_exists($option, $fieldOptions)) return $fieldOptions[$option];
        return $canBeNull?null:0;
    }

    public static function getAnswer(CustomFieldAnswer $answer) {
        $rawAnswerer = $answer->answer;
        if(is_null($rawAnswerer)) return null;
        return $answer->customField->getType()->prepareLoadFieldData($rawAnswerer);
    }

    public static function getAvailableCustomOptions(CustomField $record) : Collection{
        return $record->customOptions
            ->pluck("name","identifier");
    }

    public static function getAllCustomOptions(CustomField|CustomFieldAnswer $record) : Collection{
        if($record instanceof CustomFieldAnswer) $record = $record->customField;
        if($record->isInheritFromGeneralField()) $options = $record->generalField->customOptions;
        else $options = $record->customOptions;
        return $options->pluck("name", "identifier");
    }

    public static function getTypeConfigAttribute(CustomField|CustomFieldAnswer  $record, string $attribute) :mixed{
        if($record instanceof CustomFieldAnswer) $record = $record->customField;
        return  $record->getType()->getConfigAttribute($attribute);
    }

}
