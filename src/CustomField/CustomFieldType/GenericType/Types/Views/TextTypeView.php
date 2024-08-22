<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Lang;

class TextTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): TextInput {

        $input = TextInput::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->maxLength(FieldMapper::getOptionParameter($record,"max_length"))
            ->minLength(FieldMapper::getOptionParameter($record,"min_length"))
            ->required(FieldMapper::getOptionParameter($record,"required"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record));

        $suggestions = FieldMapper::getOptionParameter($record,"suggestions");
        if(!empty($suggestions) && !empty($suggestions[Lang::locale()])) {
            $suggestionsList = array_map(fn($data) => $data["value"] ?? "", $suggestions[Lang::locale()]);
            $input->datalist($suggestionsList);
        }



        $mask = FieldMapper::getOptionParameter($record,"alpine_mask");
        if(!empty($mask)) $input = $input->mask($mask);

        return $input;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): TextEntry {
        return TextEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record). ":")
            ->state(FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();
    }

}
