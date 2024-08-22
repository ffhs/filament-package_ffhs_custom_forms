<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;

class DateTimeTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): DateTimePicker {
        return DateTimePicker::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->label(FieldMapper::getLabelName($record))
            ->helperText(FieldMapper::getToolTips($record))
            ->format(self::getFormat($record))
;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): TextEntry {
        return TextEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->dateTime(self::getFormat($record->customField))
            ->label(FieldMapper::getLabelName($record))
            ->state(FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();
    }

    private static function getFormat(CustomField $customField):String{
        if(is_null($customField->options)) return "Y-m-d h:i:s";
        return  array_key_exists("format",$customField->options)
        && !is_null($customField->options["format"])
        && !empty($customField->options["format"])
            ?$customField->options["format"]:"Y-m-d h:i:s";
    }

}
