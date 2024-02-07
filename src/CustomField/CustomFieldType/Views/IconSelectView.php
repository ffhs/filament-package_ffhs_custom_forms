<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\IconInput;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class IconSelectView implements FieldTypeView
{


    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {
        return IconInput::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->label($type::getLabelName($record))
            ->modifyTextInputUsing(fn(TextInput $textInput)=> $textInput
                ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
                ->helperText($type::class::getToolTips($record))
                ->label($type::class::getLabelName($record))
            );

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        try {
            $icon = new HtmlString(Blade::render("<x-".$type->answare($record). " class=\"h-20 w-20\" />"));
        }catch (\InvalidArgumentException){
            $icon = "";

        }
        return TextEntry::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->label($type::getLabelName($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->state($icon); //I Dont now why that isnt working
    }

}
