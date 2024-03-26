<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\IconInput;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class IconSelectView implements FieldTypeView
{


    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {
        return IconPicker::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->label(FormMapper::getLabelName($record))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->helperText(FormMapper::getToolTips($record));

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        try {
            $icon = new HtmlString(Blade::render("<x-".FormMapper::getAnswer($record). " class=\"h-20 w-20\" />"));
        }catch (\InvalidArgumentException){
            $icon = "";

        }
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label(FormMapper::getToolTips($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->state($icon); //I Dont now why that isnt working
    }

}
