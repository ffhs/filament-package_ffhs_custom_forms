<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;

class KeyValueTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        return KeyValue::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getLabelName($record))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->reorderable(FormMapper::getOptionParameter($record,"reorderable"))
            ->editableKeys(FormMapper::getOptionParameter($record,"editableKeys"))
            ->editableValues(FormMapper::getOptionParameter($record,"editableValues"))
;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $answerer =FormMapper::getAnswer($record);
        $answerer = empty($answerer)?"":$answerer;

        return KeyValueEntry::make(FormMapper::getIdentifyKey($record))
                ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
                ->label(FormMapper::getLabelName($record). ":")
                ->columnSpanFull()
                ->inlineLabel()
                ->state($answerer);
    }
}
