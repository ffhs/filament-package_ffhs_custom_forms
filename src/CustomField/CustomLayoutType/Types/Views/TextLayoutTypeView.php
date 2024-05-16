<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TextLayoutTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Placeholder {

        $text = FormMapper::getOptionParameter($record,"text_de"); //ToDo translate

        return  Placeholder::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->content(new HtmlString($text))
            ->label("");
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return \Filament\Infolists\Components\Group::make()->hidden();

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
