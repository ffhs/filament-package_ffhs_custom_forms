<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Support\Components\Component;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    //ToDo fix infolust stuff

    protected function modifyComponent(
        Component $component,
        EmbedCustomField|CustomFieldAnswer $field,
        bool $isInfolist,
        array $ignoredOptions = [],

    ): mixed {
        /**@var $typeOption TypeOption */
        foreach ($field->getType()->getFlattenExtraTypeOptions() as $key => $typeOption) {
            if (in_array($key, $ignoredOptions, true)) {
                continue;
            }

            $value = $this->getOptionParameter($field, $key);
            if ($isInfolist) {
                $typeOption->modifyInfolistComponent($component, $value); //ToDo null value
            } else {
                $typeOption->modifyFormComponent($component, $value); //ToDo null value
            }
        }

        if ($field->isGeneralField()) {
            foreach ($field->getType()->getFlattenGeneralTypeOptions() as $key => $typeOption) {
                if (in_array($key, $ignoredOptions, true)) {
                    continue;
                }

                $value = $this->getOptionParameter($field, $key);
                if ($isInfolist) {
                    $typeOption->modifyInfolistComponent($component, $value); //ToDo null value
                } else {
                    $typeOption->modifyFormComponent($component, $value); //ToDo null value
                }
            }
        }


        if (method_exists($component, 'label')) {
            $label = $this->getLabelName($field);
            $component = empty($label) ? $component->hiddenLabel() : $component->label($label);
        }

        if (!$isInfolist) {
            return $component;
        }

        return $component
            ->state($this->getAnswer($field))
            ->inlineLabel()
            ->columnSpanFull();
    }

    protected function makeComponent(
        string $class,
        EmbedCustomField|CustomFieldAnswer $field,
        bool $isInfolist,
        array $ignoredOptions = []
    ): Component {
        $component = $class::make($this->getIdentifyKey($field));
        return $this->modifyComponent($component, $field, $isInfolist, $ignoredOptions);

    }
}
