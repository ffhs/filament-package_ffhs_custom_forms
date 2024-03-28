<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Get;

class EditCustomFormFieldFunctions
{

    public static function getFieldTypeFromRawDate(array $data): ?CustomFieldType {
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        $isTemplate = array_key_exists("template_id",$data)&& !is_null($data["template_id"]);
        if($isTemplate) return new TemplateFieldType();
        return $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);
    }

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


    public static function getFieldsWithProperty (array $customFields, string $property):array  {
        $foundFields = array_filter(
            array_values($customFields),
            fn($field)=> !empty($field[$property])
        );
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

    public static function setCustomFieldInRepeater(array $data, Get $get, $set, ?array $arguments = null): void {
        $fields = $get("custom_fields");
        if (is_null($arguments)) $fields[uniqid()] = $data;
        else $fields[$arguments["item"]] = $data;
        $set("custom_fields", $fields);
    }

    public static function useTemplateUsedGeneralFields(int $templateId, Get $get): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = EditCustomFormFieldFunctions::getUsedGeneralFieldIds($get("custom_fields"));
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

}
