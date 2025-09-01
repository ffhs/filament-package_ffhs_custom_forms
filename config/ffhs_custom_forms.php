<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\DateRangeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\DateType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\FileUploadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\SpaceType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DefaultFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\ChangeOptionsEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\DisabledEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\HideEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\RequiredEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\VisibleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\AlwaysRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\IsInfolistTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\ValueEqualsRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default\CustomFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default\GeneralFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default\TemplateFieldAdder;

return [
    'default_column_count' => 4,

    'form_configurations' => [
        DefaultFormConfiguration::class => [],
    ],

    'default_form_configuration' => [
        'editor' => [
            'field_adders' => [
                CustomFieldTypeAdder::class,
                TemplateFieldAdder::class,
                GeneralFieldAdder::class
            ],
        ],
        'view_modes' => [],
        'rule' => [
            'trigger' => [
                IsInfolistTrigger::class,
                ValueEqualsRuleTrigger::class,
                AlwaysRuleTrigger::class,
            ],
            'event' => [
                HideEvent::class,
                VisibleEvent::class,
                DisabledEvent::class,
                RequiredEvent::class,
                ChangeOptionsEvent::class,
            ],
        ],
        'types_settings' => [
            'download_file' => [
                'save_path' => '/custom-form-plugin/custom-fields/specified-data',
                'disk' => 'local',
            ],
            'image_layout' => [
                'save_path' => '/custom-form-plugin/images',
                'disk' => 'public',
            ],
            FileUploadType::identifier() => [
                'files' => [
                    'url_prefix' => null, // Null means default path
                    'save_path' => '/custom-form-plugin/custom-fields/uploaded',
                    'disk' => 'local',
                ],
                'images' => [
                    'url_prefix' => null, // Null means default path
                    'save_path' => '/custom-form-plugin/uploaded-images',
                    'disk' => 'public',
                ]
            ],
            'selectable_field_types' => [
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
            ],
        ],
    ],

    'extra_custom_field_types' => [
        TemplateFieldType::class,
    ],

    'selectable_general_field_types' => [
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
];
