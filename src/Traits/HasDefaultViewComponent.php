<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    protected function makeComponent(
        string $class,
        CustomField|CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): InfolistsComponent|FormsComponent {
        $component = $class::make($this->getIdentifyKey($record));

        if ($component instanceof FormsComponent) {
            return $this->modifyFormComponent($component, $record, $ignoredOptions);
        }

        return $this->modifyInfolistComponent($component, $record, $ignoredOptions);
    }

    protected function modifyFormComponent(
        FormsComponent $component,
        CustomField $record,
        array $ignoredOptions = []
    ): FormsComponent {
        foreach ($record->getType()->getFlattenExtraTypeOptions() as $key => $typeOption) {
            if (in_array($key, $ignoredOptions, true)) {
                continue;
            }

            $value = $this->getOptionParameter($record, $key);
            $typeOption->modifyFormComponent($component, $value); //ToDo null value
        }

        if ($record->isGeneralField()) {
            foreach ($record->getType()->getFlattenGeneralTypeOptions() as $key => $typeOption) {
                if (in_array($key, $ignoredOptions, true)) {
                    continue;
                }

                $value = $this->getOptionParameter($record, $key);
                $typeOption->modifyFormComponent($component, $value); //ToDo null value
            }
        }

        return $component->label($this->getLabelName($record));
    }

    protected function modifyInfolistComponent(
        InfolistsComponent $component,
        CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): InfolistsComponent {
        return $component
            ->columnStart($this->getOptionParameter($record, 'new_line'))
            ->label($this->getLabelName($record))
            ->state($this->getAnswer($record))
            ->inlineLabel()
            ->columnSpanFull();
    }
}
