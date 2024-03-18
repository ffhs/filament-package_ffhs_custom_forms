<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;

use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;

class DateTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): DatePicker {
        return DatePicker::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->label(FormMapper::getLabelName($record))
            ->helperText(FormMapper::getToolTips($record))
            ->format(self::getFormat($record))
            ->required($record->required);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->dateTime(self::getFormat($record->customField))
            ->label(FormMapper::getLabelName($record). ":")
            ->columnSpanFull()
            ->state(FormMapper::getAnswer($record))
            ->inlineLabel();
    }


    private static function getFormat(CustomField $customField):String{
        if(is_null($customField->options)) return "Y-m-d";
        return  array_key_exists("format",$customField->options)
        && !is_null($customField->options["format"])
        && !empty($customField->options["format"])
            ?$customField->options["format"]:"Y-m-d";
    }

}
