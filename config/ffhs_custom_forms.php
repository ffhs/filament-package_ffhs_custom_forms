<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\SpaceType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors\ValueEqualsRuleAnchor;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type\ChangeOptionRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type\DisabledRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type\HiddenRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type\RequiredRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\CustomTabCustomEggType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\TabsCustomNestType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\WizardCustomNestType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\WizardStepCustomEggType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormEditorValidation\FormEditorGeneralFieldValidation;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder\CustomFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder\GeneralFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder\TemplateAdder;

return [

    'cache_duration'=> 1,
    'default_column_count' => 4,

    "field_rule_anchor_types"=>[
        ValueEqualsRuleAnchor::class,
    ],

    "field_rule_types"=>[
        RequiredRuleType::class,
        HiddenRuleType::class,
        DisabledRuleType::class,
        ChangeOptionRuleType::class,
    ],

    "forms"=>[

    ],

    'view_modes' => [

    ],

    'custom_form_editor_validations' => [
        FormEditorGeneralFieldValidation::class
    ],

    'editor_field_adder' => [
        GeneralFieldAdder::class,
        TemplateAdder::class,
        CustomFieldAdder::class
    ],

    "custom_field_types" => [
        TemplateFieldType::class,

        TextType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        CheckboxType::class,
        DateTimeType::class,
        DateType::class,

        TagsType::class,
        KeyValueType::class,


        ColorPickerType::class,
        IconSelectType::class,

        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,

        SectionType::class,
        FieldsetType::class,
        GroupType::class,
        TitleType::class,
        TextLayoutType::class,
        DownloadType::class,
        ImageLayoutType::class,
        SpaceType::class,

        TabsCustomNestType::class,
        CustomTabCustomEggType::class,
        WizardCustomNestType::class,
        WizardStepCustomEggType::class,
    ],
    "selectable_field_types" => [
        CheckboxType::class,
        TextType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        DateTimeType::class,
        DateType::class,

        TagsType::class,
        KeyValueType::class,


        ColorPickerType::class,
        IconSelectType::class,

        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,

        SectionType::class,
        FieldsetType::class,
        GroupType::class,
        TitleType::class,
        TextLayoutType::class,
        DownloadType::class,
        ImageLayoutType::class,
        SpaceType::class,

        TabsCustomNestType::class,
        WizardCustomNestType::class,

    ],

    "selectable_general_field_types"=>[
        CheckboxType::class,
        TextType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        DateTimeType::class,
        DateType::class,

        TagsType::class,
        KeyValueType::class,


        ColorPickerType::class,
        IconSelectType::class,

        SelectType::class,
        RadioType::class,
        CheckboxListType::class,
        ToggleButtonsType::class,
    ],


    "field_settings"=>[
        "download_file" => [
            "save_path" => "/custom-form-plugin/custom-fields/specified-data",
            "disk" => "local",
        ],
        'image_layout' => [
            "save_path" => "/custom-form-plugin/images",
            "disk" => "public",
        ]
    ],


];
