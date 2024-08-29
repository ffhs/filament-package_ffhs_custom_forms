<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\IconEntry;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconSelectView implements FieldTypeView
{


    public static function getFormComponent(CustomFieldType $type, CustomField $record,
                                            array           $parameter = []): Component {
        return IconPicker::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->label(FieldMapper::getLabelName($record))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label"))
            ->helperText(FieldMapper::getToolTips($record));

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): \Filament\Infolists\Components\Component {
        return IconEntry::make(FieldMapper::getIdentifyKey($record))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record))
            ->state(FieldMapper::getAnswer($record))
            ->icon(FieldMapper::getAnswer($record))
            ->columnSpanFull()
            ->inlineLabel();

    }

}
