<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;

class CheckboxTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Checkbox {
        return Checkbox::make($record->customField->identify_key)
            ->label($type::class::getLabelName($record->customField))
            ->helperText($type::class::getToolTips($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): IconEntry {
        return IconEntry::make($record->customField->identify_key)
            ->label($type::class::getLabelName($record->customFieldVariation->customField))
            ->state($record->answare)
            ->inlineLabel()
            ->boolean();
    }

}
