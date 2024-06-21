<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\KeyValueEntry;

class KeyValueTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        return KeyValue::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->reorderable(FieldMapper::getOptionParameter($record,"reorderable"))
            ->editableKeys(FieldMapper::getOptionParameter($record,"editableKeys"))
            ->editableValues(FieldMapper::getOptionParameter($record,"editableValues"))
;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $answerer =FieldMapper::getAnswer($record);
        $answerer = empty($answerer)?"":$answerer;

        return KeyValueEntry::make(FieldMapper::getIdentifyKey($record))
                ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
                ->label(FieldMapper::getLabelName($record). ":")
                ->columnSpanFull()
                ->inlineLabel()
                ->state($answerer);
    }
}
