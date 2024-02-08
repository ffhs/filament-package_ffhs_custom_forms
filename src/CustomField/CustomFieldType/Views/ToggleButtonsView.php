<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;

class ToggleButtonsView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {

        $toggle = ToggleButtons::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->options($type->getAvailableCustomOptions($record))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required);



        return $toggle;
    }

}
