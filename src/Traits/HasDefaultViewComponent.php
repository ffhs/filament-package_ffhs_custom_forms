<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Support\Components\Component;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    //ToDo fix infolust stuff
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
    ): Component {
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
            $label = $this->getLabelName($record);
            return empty($label) ? $component->hiddenLabel() : $component->label($label);
        }
        return $component;
    }

    protected function modifyInfolistComponent(
        Component $component,
        CustomFieldAnswer $record,
        array $ignoredOptions = []
    ): Component {
        /**@var $typeOption TypeOption */
        foreach ($record->getType()->getFlattenExtraTypeOptions() as $key => $typeOption) {
            if (in_array($key, $ignoredOptions, true)) {
                continue;
            }

            $value = $this->getOptionParameter($record, $key);
            $typeOption->modifyInfolistComponent($component, $value); //ToDo null value
        }

        if ($record->isGeneralField()) {
            foreach ($record->getType()->getFlattenGeneralTypeOptions() as $key => $typeOption) {
                if (in_array($key, $ignoredOptions, true)) {
                    continue;
                }

                $value = $this->getOptionParameter($record, $key);
                $typeOption->modifyInfolistComponent($component, $value); //ToDo null value
            }
        }
        $component
            ->state($this->getAnswer($record))
            ->inlineLabel()
            ->columnSpanFull();

        if (method_exists($component, 'label')) {
            $label = $this->getLabelName($record);
            return empty($label) ? $component->hiddenLabel() : $component->label($label);
        }
        return $component;
    }
}
