<?php

use App\Domain\CustomField\Types\CheckboxType;
use App\Domain\CustomField\Types\DateTimeType;
use App\Domain\CustomField\Types\DateType;
use App\Domain\CustomField\Types\EmailType;
use App\Domain\CustomField\Types\NumberType;
use App\Domain\CustomField\Types\TextAreaType;
use App\Domain\CustomField\Types\TextType;

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

    ]
];
