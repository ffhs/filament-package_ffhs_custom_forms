<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class EmailTypeView implements FieldTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): TextInput {
        return TextInput::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getToolTips($record))
            ->required($record->required);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label($type::class::getLabelName($record). ":")
            ->state(FormMapper::getAnswer($record))
            ->columnSpanFull();
    }



}
