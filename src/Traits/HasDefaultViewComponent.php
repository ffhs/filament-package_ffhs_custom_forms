<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Component;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    /**
     * @template T of Component
     * @param T $component
     * @return T
     */
    protected function modifyComponent(
        Component $component,
        EmbedCustomField|EmbedCustomFieldAnswer $fieldRaw,
        bool $isEntry,
        array $ignoredOptions = [],

    ): Component {
        /**@var $typeOption TypeOption */
        $field = $fieldRaw instanceof EmbedCustomField ? $fieldRaw : $fieldRaw->getCustomField();

        foreach ($field->getType()->getFlattenExtraTypeOptions() as $key => $typeOption) {
            if (in_array($key, $ignoredOptions, true)) {
                continue;
            }

            $value = $this->getOptionParameter($field, $key);
            if ($isEntry) {
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
                if ($isEntry) {
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

        if (!$isEntry) {
            return $component;
        }

        if ($fieldRaw instanceof EmbedCustomFieldAnswer && method_exists($component,
                'state') && $component instanceof Entry) {
            $component = $component->state($this->getAnswer($fieldRaw));
        }


        return $component
            ->inlineLabel()
            ->columnSpanFull();
    }

    /**
     * @template T of Component
     * @param class-string<T> $class
     * @return T
     */
    protected function makeComponent(
        string $class,
        EmbedCustomField|EmbedCustomFieldAnswer $field,
        bool $isInfolist,
        array $ignoredOptions = []
    ): Component {
        $component = $class::make($this->getIdentifyKey($field));
        return $this->modifyComponent($component, $field, $isInfolist, $ignoredOptions);
    }
}
