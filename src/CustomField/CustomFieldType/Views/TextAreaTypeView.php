<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

class TextAreaTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Textarea {
        return Textarea::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->maxLength($type->getOptionParameter($record,"max_length"))
            ->minLength($type->getOptionParameter($record,"min_length"))
            ->autosize($type->getOptionParameter($record,"auto_size"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {
        return TextEntry::make($type::getIdentifyKey($record->customFieldVariation))
            ->state($record->answer)
            ->label($type::class::getLabelName($record->customFieldVariation))
            ->inlineLabel();
    }
}
