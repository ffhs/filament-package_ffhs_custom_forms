<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;

class SelecTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {
        return Select::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->helperText($type::class::getToolTips($record))
            ->label($type::class::getLabelName($record))
            ->required($record->required)
            ->options(fn()=> $record->customOptions->pluck("name_de","identifier"));//ToDo Translate
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): IconEntry {
        return IconEntry::make($type::getIdentifyKey($record))
            ->label($type::class::getLabelName($record). ":")
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->state(is_null($record->answer)? false : $record->answer)
            ->inlineLabel()
            ->boolean();
    }

}
