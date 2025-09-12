<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Support\Components\Component;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    protected function makeComponent(
        string $class,
        CustomField|CustomFieldAnswer $record,
        bool $isInfolist,
        array $ignoredOptions = []
    ): Component {
        $component = $class::make($this->getIdentifyKey($record));

        return $isInfolist ?
            $this->modifyInfolistComponent($component, $record, $ignoredOptions) :
            $this->modifyFormComponent($component, $record, $ignoredOptions);
    }

    protected function modifyFormComponent(
        Component $component,
        CustomField $record,
        array $ignoredOptions = []
    ): Component { //toDo what????
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

        if (method_exists($component, 'label')) {
            return $component->label($this->getLabelName($record));
        }
        return $component;
    }

    protected function modifyInfolistComponent(
        Component $component,
        CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): Component {
        return $component
            ->columnStart($this->getOptionParameter($record, 'new_line'))
            ->label($this->getLabelName($record))
            ->state($this->getAnswer($record))
            ->inlineLabel()
            ->columnSpanFull();
    }
}
