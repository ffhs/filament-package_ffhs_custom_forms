<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

class TextAreaTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Textarea {
        return Textarea::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->maxLength(FormMapper::getOptionParameter($record,"max_length"))
            ->minLength(FormMapper::getOptionParameter($record,"min_length"))
            ->autosize(FormMapper::getOptionParameter($record,"auto_size"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record));
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component {
        return TextEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label($type::class::getLabelName($record). ":")
            ->state(FormMapper::getAnswer($record))
            ->columnSpanFull();
    }
}
