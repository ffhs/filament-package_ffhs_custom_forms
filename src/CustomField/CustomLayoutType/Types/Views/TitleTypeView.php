<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TitleTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Placeholder {

        $title = self::getTitle($record);

        return  Placeholder::make(FormMapper::getIdentifyKey($record))
            ->content(new HtmlString($title))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->helperText(FormMapper::getToolTips($record))
            ->label("");
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return TextEntry::make(FormMapper::getIdentifyKey($record))->label("")->state("");

        $title = self::getTitle($record);
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label("")
            ->state(new HtmlString($title))
            ->columnSpanFull()
            ->inlineLabel();
    }


    private static function getTitle($record): string {
        $titleSize = FormMapper::getOptionParameter($record, "title_size");

        if($titleSize == 3) $textClass = 'class="text-xl';
        else  $textClass = 'class="text-'.(4-$titleSize).'xl"';
        $titleText = FormMapper::getLabelName($record);
        return '<h'.$titleSize.' '.$textClass.'>'.$titleText.'<h'.$titleSize.'> ';
    }

}
