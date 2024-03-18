<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\HasCustomOptionInfoListView;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;

class CheckboxListTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        return CheckboxList::make(FormMapper::getIdentifyKey($record))
           ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->columns(FormMapper::getOptionParameter($record,"columns"))
            ->options($type->getAvailableCustomOptions($record))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getLabelName($record))
            ->required($record->required);
    }


}
