<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class NumberTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): TextInput {

        return TextInput::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->minValue($type->getOptionParameter($record,"min_value"))
            ->maxValue($type->getOptionParameter($record,"max_value"))
            ->label($type::getLabelName($record))
            ->required($record->required)
            ->numeric();
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->label($type::getLabelName($record). ":")
            ->state($type->answare($record))
            ->columnSpanFull();
    }



}
