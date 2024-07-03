<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class NumberTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): TextInput {

        return TextInput::make(FieldMapper::getIdentifyKey($record))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->minValue(FieldMapper::getOptionParameter($record,"min_value"))
            ->maxValue(FieldMapper::getOptionParameter($record,"max_value"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))

            ->numeric();
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): TextEntry {
        return TextEntry::make(FieldMapper::getIdentifyKey($record). ":")
           // ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->tooltip(FieldMapper::getToolTips($record). ":")
            ->label(FieldMapper::getLabelName($record). ":")
            ->state(FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();
    }



}
