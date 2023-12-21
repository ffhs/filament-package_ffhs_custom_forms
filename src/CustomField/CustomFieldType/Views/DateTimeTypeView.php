<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;

class DateTimeTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): DateTimePicker {
        return DateTimePicker::make($record->identify_key)
            ->label($type::class::getLabelName($record->customField))
            ->helperText($type::class::getToolTips($record))
            ->format(self::getFormat($record));
    }

    public static function getViewComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make($record->customField->identify_key)
            ->dateTime(self::getFormat($record->customField))
            ->label($type::class::getLabelName($record->customField))
            ->state($record->answare)
            ->inlineLabel();
    }

    private static function getFormat(CustomField $customField):string{
        if(is_null($customField->field_options)) return "Y-m-d h:i:s";
        return  array_key_exists("format",$customField->field_options)
        && !is_null($customField->field_options["format"])
        && !empty($customField->field_options["format"])
            ?$customField->field_options["format"]:"Y-m-d h:i:s";
    }

}
