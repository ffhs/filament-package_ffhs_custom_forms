<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class ToggleButtonsView implements FieldTypeView
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getInfolistComponentParent;
    }

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {

        $toggles = ToggleButtons::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))

            ->multiple($type->getOptionParameter($record,"multiple"))
            ->columns($type->getOptionParameter($record,"columns"))
            ->helperText($type::getToolTips($record))
            ->label($type::getLabelName($record))
            ->required($record->required);

        if($type->getOptionParameter($record,"grouped")) $toggles->grouped();
        else if($type->getOptionParameter($record,"in_line_label")) $toggles->inlineLabel();
        else $toggles->columnSpan($type->getOptionParameter($record,"column_span"));

        if($type->getOptionParameter($record,"boolean")) $toggles->boolean();
        else $toggles->options($type->getAvailableCustomOptions($record));

        return $toggles;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!$type->getOptionParameter($record, "boolean"))
            return self::getInfolistComponentParent($type,$record,$parameter);
        else
            return IconEntry::make($type::getIdentifyKey($record))
                ->label($type::getLabelName($record). ":")
                ->state($type->answare($record))
                ->columnSpanFull()
                ->inlineLabel()
                ->boolean();

    }

}
