<?php


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types\TextType;

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
];
