<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;

trait HasDefaultViewComponent
{
    protected static function makeComponent(
        string $class,
        CustomField|CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): InfolistsComponent|FormsComponent {
        $component = $class::make(FieldMapper::getIdentifyKey($record));

        if ($component instanceof FormsComponent) {
            return static::modifyFormComponent($component, $record, $ignoredOptions);
        } else {
            return static::modifyInfolistComponent($component, $record, $ignoredOptions);
        }
    }

    protected static function modifyFormComponent(
        FormsComponent $component,
        CustomField $record,
        array $ignoredOptions = []
    ): FormsComponent {
        foreach ($record->getType()->getFlattenExtraTypeOptions() as $key => $typeOption) {
            if (in_array($key, $ignoredOptions)) {
                continue;
            }

            $value = FieldMapper::getOptionParameter($record, $key);
            $typeOption->modifyFormComponent($component, $value); //ToDo null value
        }

        if ($record->isGeneralField()) {
            foreach ($record->getType()->getFlattenGeneralTypeOptions() as $key => $typeOption) {
                if (in_array($key, $ignoredOptions)) {
                    continue;
                }

                $value = FieldMapper::getOptionParameter($record, $key);
                $typeOption->modifyFormComponent($component, $value); //ToDo null value
            }
        }

        return $component
            ->label(FieldMapper::getLabelName($record));
    }

    protected static function modifyInfolistComponent(
        InfolistsComponent $component,
        CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): InfolistsComponent {
        return $component
            ->columnStart(FieldMapper::getOptionParameter($record, 'new_line'))
            ->label(FieldMapper::getLabelName($record))
            ->state(FieldMapper::getAnswer($record))
            ->inlineLabel()
            ->columnSpanFull();
    }
}
