<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{

    use HasCustomOptionInfoListViewWithBoolean;

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
        else $toggles->options(FormMapper::getAvailableCustomOptions($record));

        return $toggles;
    }


}
