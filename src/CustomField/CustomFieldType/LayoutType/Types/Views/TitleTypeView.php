<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TitleTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,array  $parameter = []): Placeholder {

        $title = self::getTitle($record);
        $helpText = FieldMapper::getOptionParameter($record, "helper_text");

        return  Placeholder::make(FieldMapper::getIdentifyKey($record))
            ->content(new HtmlString($title))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->helperText($helpText?new HtmlString($helpText):null)
            ->label("");
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): Component {

        if(!FieldMapper::getOptionParameter($record,"show_in_view"))
            return \Filament\Infolists\Components\Group::make()->hidden();

        $title = self::getTitle($record);
        return TextEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label("")
            ->state(new HtmlString($title))
            ->columnSpanFull()
            ->inlineLabel();
    }


    private static function getTitle($record): string {
        $titleSize = FieldMapper::getOptionParameter($record, "title_size");

        if($titleSize == 3) $textClass = 'class="text-xl"';
        else if($titleSize < 3) $textClass = 'class="text-'.(4-$titleSize).'xl"';
        else {
            if($titleSize == 4) $textClass = 'class="text-lg"';
            else if($titleSize == 5) $textClass = 'class="text-base"';
            else if($titleSize == 6) $textClass = 'class="text-sm"';
            else $textClass = 'class="text-xs"';
        }
        $titleText = FieldMapper::getLabelName($record);
        return '<h'.$titleSize.' '.$textClass.'>'.$titleText.' </h'.$titleSize.'> ';
    }

}
