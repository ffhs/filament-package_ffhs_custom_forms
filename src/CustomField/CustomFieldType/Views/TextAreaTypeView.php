<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;

class TextAreaTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Textarea {
        return Textarea::make($type::getIdentifyKey($record))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        return TextEntry::make($type::getIdentifyKey($record))
            ->state($record->answer)
            ->label($type::class::getLabelName($record->customFieldVariation))
            ->inlineLabel();
    }
}
