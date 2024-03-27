<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;

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
    public static function flattenWithoutKeys($array): array {
        $results = [];

        foreach ($array as $value) {
            if (is_array($value) && ! empty($value)) $results = array_merge($results, self::flattenWithoutKeys($value));
            else $results[] = $value;
        }

        return $results;
    }
    public static function flattArrayOneLayer($array): array {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)){
                $subResult = [];
                foreach ($value as $key1 => $value1) {
                    $subResult[$key1]=$value1;
                }
                $results = array_merge($results, $subResult);
            }
            else $results[$key] = $value;
        }


        return $results;
    }

    public static function flattDownToCustomFields(mixed $data): mixed {
        for ($i = 0; $i <= 10; $i++) {
            if (array_key_exists("custom_fields", $data)) break;
            //If the CustomForm isn't direct in the first layer, it will break it up to the layer with the custom_fields
            $data = CustomFieldUtils::flattArrayOneLayer($data);
        }
        return $data;
    }
}
