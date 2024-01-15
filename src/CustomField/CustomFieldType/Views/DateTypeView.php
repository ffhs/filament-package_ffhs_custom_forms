<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;

class DateTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): DatePicker {
        return DatePicker::make($type::getIdentifyKey($record))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->label($type::class::getLabelName($record))
            ->helperText($type::class::getToolTips($record))
            ->format(self::getFormat($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make($type::getIdentifyKey($record->customFieldVariation))
            ->dateTime(self::getFormat($record->customFieldVariation))
            ->label($type::class::getLabelName($record->customFieldVariation))
            ->state($record->answer)
            ->inlineLabel();
    }

    private static function getFormat(CustomFieldVariation $customField):string{
        if(is_null($customField->options)) return "Y-m-d";
        return  array_key_exists("format",$customField->options)
        && !is_null($customField->options["format"])
        && !empty($customField->options["format"])
            ?$customField->options["format"]:"Y-m-d";
    }

}
