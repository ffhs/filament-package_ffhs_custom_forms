<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\DateRangeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\FileUploadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\SpaceType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\ChangeOptionsEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\DisabledEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\HideEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\RequiredEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\VisibleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\AlwaysRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\IsInfolistTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\ValueEqualsRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default\CustomFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default\GeneralFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default\TemplateFieldAdder;

return [

    'cache_duration'=> 1,
    'save_stopper_time'=> 1,
    'default_column_count' => 4,

    "rule"=>[
        "trigger" =>[
            IsInfolistTrigger::class,
            ValueEqualsRuleTrigger::class,
            AlwaysRuleTrigger::class,
        ],
        "event"=>[
            HideEvent::class,
            VisibleEvent::class,
            DisabledEvent::class,
            RequiredEvent::class,
            ChangeOptionsEvent::class,
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
        DateRangeType::class,
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
        RepeaterLayoutType::class,
        FieldsetType::class,
        GroupType::class,
        TitleType::class,
        TextLayoutType::class,
        DownloadType::class,
        ImageLayoutType::class,
        SpaceType::class,

        //TabsCustomNestType::class, ToDo Fix
        //CustomTabCustomEggType::class,
        // WizardCustomNestType::class,
        //WizardStepCustomEggType::class,
    ],

    "selectable_field_types" => [
        CheckboxType::class,
        TextType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        DateTimeType::class,
        DateType::class,
        DateRangeType::class,
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
        RepeaterLayoutType::class,
        FieldsetType::class,
        GroupType::class,
        TitleType::class,
        TextLayoutType::class,
        DownloadType::class,
        ImageLayoutType::class,
        SpaceType::class,

        // TabsCustomNestType::class,
        // WizardCustomNestType::class,
    ],

    "selectable_general_field_types"=>[
        CheckboxType::class,
        TextType::class,
        EmailType::class,
        NumberType::class,
        TextAreaType::class,
        DateTimeType::class,
        DateType::class,
        DateRangeType::class,

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
                "url_prefix" => null, // Null means default path
                "save_path" => "/custom-form-plugin/custom-fields/uploaded",
                "disk" => "local",
            ],
           'images' => [
               "url_prefix" => null, // Null means default path
               "save_path" => "/custom-form-plugin/uploaded-images",
               "disk" => "public",
           ]
        ]
    ],


];
