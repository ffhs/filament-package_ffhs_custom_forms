<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class DateTimeType extends CustomFieldType
{
    public static function getFieldName(): string {return "date-time";}

    public function getFormComponent(CustomField $record, string $viewMode = "default",
        array $parameter = []): Component {
        return DateTimePicker::make($record->identify_key)
            ->label(self::getLabelName($record->customField))
            ->helperText(self::getToolTips($record))
            ->format($this->getFormat($record));
    }

    public function getViewComponent(CustomFieldAnswer $record, string $viewMode = "default",
        array $parameter = []): \Filament\Infolists\Components\Component {
        return TextEntry::make($record->customField->identify_key)
            ->dateTime($this->getFormat($record->customField))
            ->label(self::getLabelName($record->customField))
            ->state($record->answare)
            ->inlineLabel();
    }


    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("format")
                ->placeholder("Y-m-d H:i:s")
                ->columnSpanFull()

        ];
    }
    public function getExtraOptionFields(): array {
        return [
            'format'=>null,
        ];
    }


    private  function getFormat(CustomField $customField):string{
        if(is_null($customField->field_options)) return "Y-m-d h:i:s";
        return  array_key_exists("format",$customField->field_options)
        && !is_null($customField->field_options["format"])
        && !empty($customField->field_options["format"])
            ?$customField->field_options["format"]:"Y-m-d h:i:s";
    }
}
