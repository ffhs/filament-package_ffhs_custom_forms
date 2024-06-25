<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Get;

class EditCustomFormHelper
{



    public static function getUsedGeneralFieldIds(array $customFields):array {

        //GeneralFieldIds From GeneralFields
        $generalFields = self::getFieldsWithProperty($customFields,"general_field_id");
        $generalFieldId = array_map(fn($used) => $used["general_field_id"],$generalFields);


        //GeneralFieldIds From Templates
        $templateData = self::getFieldsWithProperty($customFields,"template_id");
        $templateIds = array_map(fn($used) => $used["template_id"],$templateData);
        foreach ($templateIds as $templateId){
            $genFields = CustomForm::cached($templateId)?->generalFields->pluck("id")->toArray();
            $generalFieldId = array_merge($generalFieldId,$genFields);
        }

        return $generalFieldId;
    }



    public static function getFieldsWithProperty (array $customFields, ?string $property=null):array  {

        if(is_null($property)) {
            $foundFields = $customFields;
            $foundFields = array_combine(array_keys($foundFields), array_map(function($field){
                unset($field["custom_fields"]);
                return $field;
            },$foundFields));
        }
        else{
            $foundFields = array_filter(
                array_values($customFields),
                fn($field)=> !empty($field[$property])
            );
        }

        $nestedFields = collect(array_values($customFields))
            ->filter(fn($field)=> !empty($field["custom_fields"]))
            ->map(fn($field)=> $field["custom_fields"]);



        $foundFields=  array_filter($foundFields, fn($value)=> !is_null($value));

        if($nestedFields->count() > 0){
            $foundNestedFields = $nestedFields
                ->map(fn(array $fields)=> self::getFieldsWithProperty($fields,$property))
                ->flatten(1);
            return array_merge($foundFields, $foundNestedFields->toArray());
        }

        return $foundFields;
    }

  /*  public static function setCustomFieldInRepeater(array $data, Get $get, $set, ?array $arguments = null): void {
        $fields = $get("custom_fields");
        if (is_null($arguments)) $fields[uniqid()] = $data;
        else $fields[$arguments["item"]] = $data;
        $set("custom_fields", $fields);
    }*/

    public static function getRawStateForm($livewireComponent, $form):array {
        //Get RawSate (yeah is possible)
        return array_values($livewireComponent->getLivewire()->getCachedForms())[$form]->getRawState();
    }



    public static function getFieldData(array $state, string $item): array {
        $data = data_get($state, "data.". $item);
        $data["key"] = $item;
        return $data;
    }

}
