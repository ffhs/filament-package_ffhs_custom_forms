<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView;

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {

        return Radio::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required)
            ->options($type->getAvailableCustomOptions($record));
    }
}
