<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {

        $select = Select::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->helperText($type::getToolTips($record))
            ->label($type::getLabelName($record))
            ->required($record->required)
            ->options($type->getAvailableCustomOptions($record));

        if($type->getOptionParameter($record,"several")){
            $maxItems = $type->getOptionParameter($record,"max_select");
            $select->multiple()->minItems($record->required?$type->getOptionParameter($record,"min_select"):0);
            if($maxItems > 0)$select->maxItems($maxItems);
        }

        return $select;
    }

}
