<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class TextTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): TextInput {
        return TextInput::make($type::getIdentifyKey($record))
            ->maxLength($record->options["max_size"])
            ->helperText($type::getToolTips($record))
            ->label($type::getLabelName($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): TextEntry {
        return TextEntry::make($type::getIdentifyKey($record))
            ->state(fn(CustomFieldAnswer $record) => $record->answer)
            ->label($type::class::getLabelName($record->customField));
    }

}
