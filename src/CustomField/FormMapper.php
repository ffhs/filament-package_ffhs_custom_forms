<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
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

    public static function getOptionParameter(CustomField|CustomFieldAnswer $record, string $option){
        if($record instanceof CustomFieldAnswer) $record=$record->customField;
        if(array_key_exists($option, $record->options)) return $record->options[$option];
        //ToDo Rule
        return $record->getType()->getExtraOptionFields()[$option];
    }

    public static function getAnswer(CustomFieldAnswer $answer) {
        $rawAnswerer = $answer->answer;
        if(is_null($rawAnswerer)) return null;
        return $answer->customField->getType()->prepareLoadFieldData($rawAnswerer);
    }

}
