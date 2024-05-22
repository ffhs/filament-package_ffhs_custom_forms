<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Lang;

class TextTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): TextInput {

        $input = TextInput::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->maxLength(FormMapper::getOptionParameter($record,"max_length"))
            ->minLength(FormMapper::getOptionParameter($record,"min_length"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getLabelName($record));

        $suggestions = FormMapper::getOptionParameter($record,"suggestions");
        if(!empty($suggestions)) $input->datalist(array_map(fn($data) => $data[Lang::locale()], $suggestions));


        $mask = FormMapper::getOptionParameter($record,"alpine_mask");
        if(!empty($mask)) $input = $input->mask($mask);

        return $input;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label(FormMapper::getLabelName($record). ":")
            ->state(FormMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();
    }

}
