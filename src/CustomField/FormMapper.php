<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class FormMapper
{

    public static function getToolTips(CustomField|CustomFieldAnswer $record) :?string{
        if($record instanceof  CustomFieldAnswer) $record = $record->customField;
        return  $record->getInheritState()["tool_tip_" . App::currentLocale()];
    }
    public static function getIdentifyKey(CustomField|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customField;
        return  $record->getInheritState()["identify_key"];
    }
    public static function getLabelName(CustomField|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customField;
        return  $record->getInheritState()["name_" . App::currentLocale()];
    }

    public static function getOptionParameter(CustomField|CustomFieldAnswer $record, string $option, bool $canBeNull = false):mixed{
        if($record instanceof CustomFieldAnswer) $record=$record->customField;
        if(is_null($record->options)) $record->options = [];
        if(array_key_exists($option, $record->options) ){
            $return = $record->options[$option];
            if(!is_null($return)) return $return;
            else if($canBeNull) return null;
        }

        //ToDo make that Rule can change options Data
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
        $options = $record->customOptions;
        return $options->pluck("name_de","identifier");//ToDo Translate
    }

    public static function getAllCustomOptions(CustomField|CustomFieldAnswer $record) : Collection{
        if($record instanceof CustomFieldAnswer) $record = $record->customField;
        if($record->isInheritFromGeneralField()) $options = $record->generalField->customOptions;
        else $options = $record->customOptions;
        return $options->pluck("name_de","identifier");//ToDo Translate
    }
}
