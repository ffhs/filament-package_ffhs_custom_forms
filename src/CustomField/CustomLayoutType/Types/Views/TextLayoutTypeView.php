<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TextLayoutTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Placeholder {

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";
        $text = FormMapper::getOptionParameter($record,"text_de"); //ToDo translate

        return  Placeholder::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->content(new HtmlString($text))
            ->label("")
            ->label($label);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return TextEntry::make(FormMapper::getIdentifyKey($record))->label("")->state("");

        $label = FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"";
        $text = FormMapper::getOptionParameter($record,"text_de"); //ToDo translate

        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->state(new HtmlString($text))
            ->columnSpanFull()
            ->label($label)
            ->inlineLabel();
    }



}
