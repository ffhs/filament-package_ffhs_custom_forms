<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\FileUploadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SpaceType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types\CustomTabCustomEggType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types\TabsCustomNestType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types\WizardCustomNestType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types\WizardStepCustomEggType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\IsInfolistTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder\CustomFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder\GeneralFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder\TemplateFieldAdder;

return [

    'cache_duration'=> 1,
    'save_stopper_time'=> 1,
    'default_column_count' => 4,

    "rule"=>[
        "trigger" =>[
        //    ValueEqualsRuleAnchor::class,
            IsInfolistTrigger::class,
        ],
        "events"=>[
            //   RequiredRuleType::class,
            //   HiddenRuleType::class,
            //  DisabledRuleType::class,
            //  ChangeOptionRuleType::class,
        ],
    ],



    "forms"=>[

    ],

    'view_modes' => [

    ],


    'editor' => [
        'field_adders' => [
            CustomFieldTypeAdder::class,
            TemplateFieldAdder::class,
            GeneralFieldAdder::class
        ],
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
        FileUploadType::class,

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
        FileUploadType::class,

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


    "type_settings"=>[
        "download_file" => [
            "save_path" => "/custom-form-plugin/custom-fields/specified-data",
            "disk" => "local",
        ],
        'image_layout' => [
            "save_path" => "/custom-form-plugin/images",
            "disk" => "public",
        ],
        'file_upload' => [
            'files' => [
                "save_path" => "/custom-form-plugin/custom-fields/uploaded",
                "disk" => "local",
            ],
           'images' => [
               "save_path" => "/custom-form-plugin/uploaded-images",
               "disk" => "public",
           ]
        ]
    ],


];
