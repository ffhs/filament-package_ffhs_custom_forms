<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;

trait HasDefaultViewComponent
{
    protected static function makeComponent(string $class, CustomField|CustomFieldAnswer $record): \Filament\Infolists\Components\Component|Component
    {
        $component = $class::make(FieldMapper::getIdentifyKey($record));
        if($component instanceof Component) return static::modifyFormComponent($component, $record);
        else return static::modifyInfolistComponent($component, $record);
    }

    protected static function modifyFormComponent(Component $component, CustomField $record): Component {
        return $component
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->helperText(FieldMapper::getOptionParameter($record, "helper_text"))
            ->inlineLabel(FieldMapper::getOptionParameter($record,"in_line_label") ?? false)
            ->required(FieldMapper::getOptionParameter($record,"required" ?? false))
            ->label(FieldMapper::getLabelName($record));

    }

    protected static function modifyInfolistComponent(\Filament\Infolists\Components\Component $component, CustomFieldAnswer $record): \Filament\Infolists\Components\Component {
        return $component
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->label(FieldMapper::getLabelName($record))
            ->state(FieldMapper::getAnswer($record))
            ->inlineLabel()
            ->columnSpanFull();
    }

}
