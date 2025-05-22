<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField;

class CustomFieldUtils
{

    public static function flatten($array): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, self::flatten($value));
            } else {
                $results[$key] = $value;
            }
        }

        return $results;
    }


    public static function flattDownToCustomFields(mixed $data): mixed
    {
        for ($i = 0; $i <= 10; $i++) {
            if (array_key_exists("custom_fields", $data)) {
                break;
            }
            // If the CustomForms isn't direct in the first layer, it will
            // break it up to the layer with the custom_fields
            $data = CustomFieldUtils::flattArrayOneLayer($data);
        }
        return $data;
    }
}
