<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

class CustomFieldUtils
{

    public static function flatten($array): array {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) $results = array_merge($results, self::flatten($value));
            else $results[$key] = $value;
        }

        return $results;
    }


    public static function flattDownToCustomFields(mixed $data): mixed {
        for ($i = 0; $i <= 10; $i++) {
            if (array_key_exists("custom_fields", $data)) break;
            // If the CustomForms isn't direct in the first layer, it will
            // break it up to the layer with the custom_fields
            $data = CustomFieldUtils::flattArrayOneLayer($data);
        }
        return $data;
    }

    public static function getFieldTypeFromRawDate(?array $data): ?CustomFieldType {
        if(is_null($data)) return null;

        $isTemplate = array_key_exists("template_id",$data)&& !is_null($data["template_id"]);
        if($isTemplate) return  TemplateFieldType::make();

        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        if($isGeneral){
            return  GeneralField::cached($data["general_field_id"])->getType();
        }
        if(empty($data["type"])) return null;
        return  CustomFieldType::getTypeFromIdentifier($data["type"]);
    }
}
