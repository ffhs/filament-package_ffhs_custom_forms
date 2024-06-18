<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{

    use HasCustomOptionInfoListViewWithBoolean;

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        $toggles = ToggleButtons::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
           // ->multiple(FormMapper::getOptionParameter($record,"multiple"))
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->helperText(FieldMapper::getToolTips($record))
            ->label(FieldMapper::getLabelName($record))
;

        if(FieldMapper::getOptionParameter($record,"grouped")) $toggles->grouped();
        else if(FieldMapper::getOptionParameter($record,"inline")) $toggles->inline();
        else $toggles->columnSpan(FieldMapper::getOptionParameter($record,"column_span"));

        if(FieldMapper::getOptionParameter($record,"boolean")) $toggles->boolean();
        else $toggles->options(FieldMapper::getAvailableCustomOptions($record));

        return $toggles;
    }


}
