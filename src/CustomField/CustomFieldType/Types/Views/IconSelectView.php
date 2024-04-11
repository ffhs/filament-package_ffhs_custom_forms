<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\IconEntry;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconSelectView implements FieldTypeView
{


    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {
        return IconPicker::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))
            ->label(FormMapper::getLabelName($record))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->helperText(FormMapper::getToolTips($record));

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {
        return IconEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->label(FormMapper::getLabelName($record). ":")
            ->state(FormMapper::getAnswer($record))
            ->icon(FormMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();

    }

}
