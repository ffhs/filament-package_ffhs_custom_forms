<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\TextInput;

class NumberTypeView extends TextTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record, array $parameter = []): TextInput {
        return parent::getFormComponent($type, $record, $parameter)
            ->columnSpan($type->getOptionParameter($record,"colum_span"))
            ->minValue($type->getOptionParameter($record,"min_value"))
            ->maxValue($type->getOptionParameter($record,"max_value"))
            ->step($type->getOptionParameter($record,"step"))
            ->maxLength(false)
            ->minLength(false)
            ->numeric();
    }


}
