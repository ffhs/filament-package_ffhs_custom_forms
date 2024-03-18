<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;

class CheckboxTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Checkbox {
        return Checkbox::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))
            ->helperText(FormMapper::getToolTips($record))
            ->label(FormMapper::getLabelName($record))
            ->required($record->required);
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): IconEntry {
        return IconEntry::make(FormMapper::getIdentifyKey($record))
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))
            ->state(is_null(FormMapper::getAnswer($record))? false : FormMapper::getAnswer($record))
            ->label(FormMapper::getLabelName($record). ":")
            ->columnSpanFull()
            ->inlineLabel()
            ->boolean();
    }

}
