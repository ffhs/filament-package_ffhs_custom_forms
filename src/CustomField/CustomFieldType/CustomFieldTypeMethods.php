<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Support\Colors\Color;

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
        return __("custom_forms.types." . $this::getFieldIdentifier());
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



    public function afterEditFieldSave(CustomField $field, array $rawData):void {

    }
    public function afterEditFieldDelete(CustomField $record):void {
    }


    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData):void {

    }

    public function mutateOnTemplateDissolve(array $data, CustomField $original):array {
        return $data;
    }


    public function nameFormEditor(array $state): string|null {
        if(empty($state["general_field_id"])) return $state["name_de"];//ToDo Translate;
        $genField = GeneralField::cached($state["general_field_id"]);
        return $genField->name_de; //ToDo Translate;
    }

    public function nameBeforeIconFormEditor(array $state):string|null {
        if(empty($state["general_field_id"])) return '';
        return new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)"));
    }
}
