<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {
        $select = Select::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getToolTips($record))
            ->required($record->required)
            ->options(FormMapper::getAvailableCustomOptions($record));

        if(FormMapper::getOptionParameter($record,"several")){
            $maxItems = FormMapper::getOptionParameter($record,"max_select");
            $select->multiple()->minItems($record->required?FormMapper::getOptionParameter($record,"min_select"):0);
            if($maxItems > 0)$select->maxItems($maxItems);
        }

        return $select;
    }

}
