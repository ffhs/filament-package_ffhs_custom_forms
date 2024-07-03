<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Get;

class EditCustomFormHelper
{

    public static function getUsedGeneralFieldIds(array $customFields):array {

        //GeneralFieldIds From GeneralFields
    /*    $generalFields = self::getFieldsWithProperty($customFields,"general_field_id");*/
        $generalFields = array_filter($customFields, fn($fieldData) => !empty($fieldData["general_field_id"]));
        $generalFieldId = array_map(fn($used) => $used["general_field_id"],$generalFields);


        //GeneralFieldIds From Templates
        $templateData = array_filter($customFields, fn($fieldData) => !empty($fieldData["template_id"]));
        $templateIds = array_map(fn($used) => $used["template_id"], $templateData);
        foreach ($templateIds as $templateId){
            $genFields = CustomForm::cached($templateId)?->generalFields->pluck("id")->toArray();
            $generalFieldId = array_merge($generalFieldId,$genFields);
        }

        return $generalFieldId;
    }



/* public static function getFieldsWithProperty (array $customFields, ?string $property=null):array  {

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
    }*/




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



    public static function addField(array $toAdd, int $position, array $formFields, ?string $key): array {
        if($key == null) $key = static::getEditKey($toAdd);

        $nestedList = NestedFlattenList::make($formFields, CustomField::class);

        $nestedList->addOnPosition($position, $toAdd, $key);

        return $nestedList->getData();
    }



    public static function removeField( string $toRemoveKey, array $fields): array {

        //Delete Structure
        $toDelete = $fields[$toRemoveKey];

        $nestedList = NestedFlattenList::make($fields, CustomField::class);

        $nestedList->removeFromPosition($toDelete['form_position']);
        return $nestedList->getData();
    }

    public static function addMultipleFields(array $toAddFields, int $position, array $fields): array{


        $nestedList = NestedFlattenList::make($fields, CustomField::class);
        $nestedList->addManyOnPosition($position, $toAddFields, true);

        return $nestedList->getData();

       /* $amountToAdd = count($toAddFields);

        //prepareToAddFields
        foreach ($toAddFields as $fieldKey => $field){
            $toAddFields[$fieldKey]["form_position"] = $field["form_position"] + $position -1;
            if(!empty($fields["layout_end_position"]))
                $toAddFields[$fieldKey]["form_position"] = $field["layout_end_position"] + $position -1;
        }


        $finalFields = $fields;
        //Rearrange Fields
        foreach ($fields as $fieldKey => $field) {
            $fieldPosition = $field["form_position"];
            $fieldEndPosition = $field["layout_end_position"] ?? null;

            if($position <= $amountToAdd + $position)
                data_set($finalFields, $fieldKey. ".form_position", $fieldPosition + $amountToAdd);
            if(!is_null($fieldEndPosition) || $position <= $fieldEndPosition)
                data_set($finalFields, $fieldKey. ".layout_end_position", $fieldEndPosition + $amountToAdd);
        }


        //Add Fields
        return array_merge($finalFields, $toAddFields);*/

    }

    public static function getEditKey(array|CustomField $toAdd)
    {
        if($toAdd instanceof CustomField)
            return  empty($field->identifier)?uniqid(): $field->identifier;


        if(array_key_exists("identifier", $toAdd) && !empty($toAdd["identifier"]))
            return $toAdd["identifier"];

        return uniqid();
    }


}
