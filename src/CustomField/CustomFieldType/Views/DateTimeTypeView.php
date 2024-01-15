<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;

class DateTimeTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): DateTimePicker {
        return DateTimePicker::make($type::getIdentifyKey($record))
            ->label($type::class::getLabelName($record))
            ->helperText($type::class::getToolTips($record))
            ->format(self::getFormat($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make($record->customFieldVariation->customField->identify_key)
            ->dateTime(self::getFormat($record->customFieldVariation))
            ->label($type::class::getLabelName($record->customFieldVariation))
            ->state($record->answare)
            ->inlineLabel();
    }

    private static function getFormat(CustomFieldVariation $customField):string{
        if(is_null($customField->options)) return "Y-m-d h:i:s";
        return  array_key_exists("format",$customField->options)
        && !is_null($customField->options["format"])
        && !empty($customField->options["format"])
            ?$customField->options["format"]:"Y-m-d h:i:s";
    }

}
