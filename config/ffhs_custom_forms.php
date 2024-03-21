<?php


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors\ValueEqualsRuleAnchor;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type\IsRequiredRuleType;

return [

    'cache_duration'=> 1,
    'default_column_count' => 8,

    "field_rule_anchor_types"=>[
        ValueEqualsRuleAnchor::class
    ],

    "field_rule_types"=>[
        IsRequiredRuleType::class
    ],

    "forms"=>[

    ],

    'view_modes' => [

    ],

    "custom_field_types" => [
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        TextType::class,
        SectionType::class,
        IconSelectType::class,
        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,
    ],
    "general_field_types"=>[
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        TextType::class,
        IconSelectType::class,
        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,
    ],

];
