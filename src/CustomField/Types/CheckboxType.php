<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\IconEntry;

class CheckboxType extends CustomFieldType
{
    public static function getFieldName(): string { return "checkbox"; }


    public function getFormComponent(CustomField $record, string $viewMode = "default",
        array $parameter = []): Component {
        return Checkbox::make($record->identify_key)
            ->label(self::getLabelName($record->customField))
            ->helperText(self::getToolTips($record));
    }

    public function getViewComponent(CustomFieldAnswer $record, string $viewMode = "default",
        array $parameter = []): \Filament\Infolists\Components\Component {
        return IconEntry::make($record->customField->identify_key)
            ->label(self::getLabelName($record->customField))
            ->state($record->answare)
            ->inlineLabel()
            ->boolean();
    }
}
