<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class EmailTypeView implements FieldTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): TextInput {
        return TextInput::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))
;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): TextEntry {
        return TextEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record). ":")
            ->state(FieldMapper::getAnswer($record))
            ->columnSpanFull();
    }



}
