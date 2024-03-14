<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;

class ToggleButtonsView implements FieldTypeView
{
    use HasCustomOptionInfoListView{
        HasCustomOptionInfoListView::getInfolistComponent as getInfolistComponentParent;
    }

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        $toggles = ToggleButtons::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->multiple(FormMapper::getOptionParameter($record,"multiple"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getLabelName($record))
            ->required($record->required);

        if(FormMapper::getOptionParameter($record,"grouped")) $toggles->grouped();
        else if(FormMapper::getOptionParameter($record,"inline")) $toggles->inline();
        else $toggles->columnSpan(FormMapper::getOptionParameter($record,"column_span"));

        if(FormMapper::getOptionParameter($record,"boolean")) $toggles->boolean();
        else $toggles->options($type->getAvailableCustomOptions($record));

        return $toggles;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        if(!FormMapper::getOptionParameter($record, "boolean"))
            return self::getInfolistComponentParent($type,$record,$parameter);
        else
            return IconEntry::make(FormMapper::getIdentifyKey($record))
                ->label(FormMapper::getLabelName($record). ":")
                ->state(FormMapper::getAnswer($record))
                ->columnSpanFull()
                ->inlineLabel()
                ->boolean();
    }

}
