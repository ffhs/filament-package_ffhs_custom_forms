<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;

class DateTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): DatePicker {
        return static::makeComponent(DatePicker::class, $record)
            ->format(self::getFormat($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): TextEntry {
        return static::makeComponent(TextEntry::class, $record)
            ->dateTime(self::getFormat($record->customField));
    }


    private static function getFormat(CustomField $customField):String{
        if(is_null($customField->options)) return "Y-m-d";
        return  array_key_exists("format",$customField->options)
        && !is_null($customField->options["format"])
        && !empty($customField->options["format"])
            ?$customField->options["format"]:"Y-m-d";
    }

}
