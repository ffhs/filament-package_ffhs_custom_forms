<?php


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TextType;

return [
    "custom_field_types" => [
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        TextType::class,
    ],

    "forms"=>[

    ],

    "general_field_types"=>[
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        TextType::class,
    ],

    'view_modes' => [

    ]
];
