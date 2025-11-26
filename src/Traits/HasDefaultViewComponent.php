<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Component;

trait HasDefaultViewComponent
{
    use CanMapFields;
    use HasStaticMake;

    /**
     * @template T of Component
     * @param T $component
     * @param array<int|string, string> $ignoredOptions
     * @return T
     */
    protected function modifyComponent(
        Component $component,
        EmbedCustomField|EmbedCustomFieldAnswer $fieldRaw,
        bool $isEntry,
        array $ignoredOptions = [],
    ): Component {
        $field = $fieldRaw instanceof EmbedCustomField ? $fieldRaw : $fieldRaw->getCustomField();
        $type = $field->getType();
        $isGeneralField = $field->isGeneralField();

        // Pre-compute all defaults once
        $fieldOptions = $field->getOptions();
        $default = $type->getDefaultTypeOptionValues();
        $generalDefault = $isGeneralField ? $type->getDefaultGeneralOptionValues() : [];

        // Apply type options
        $this->applyTypeOptions(
            $type->getFlattenExtraTypeOptions(),
            $component,
            $ignoredOptions,
            $isEntry,
            $fieldOptions,
            $default,
            $generalDefault
        );

        // Apply general options if applicable
        if ($isGeneralField) {
            $this->applyTypeOptions(
                $type->getFlattenGeneralTypeOptions(),
                $component,
                $ignoredOptions,
                $isEntry,
                $fieldOptions,
                $default,
                $generalDefault
            );
        }

        // Handle label
        if (method_exists($component, 'label') && method_exists($component, 'hiddenLabel')) {
            $label = $this->getLabelName($field);
            $component = empty($label) ? $component->hiddenLabel() : $component->label($label);
        }

        // Early return for non-entry components
        if (!$isEntry) {
            return $component;
        }

        // Handle answer state for entries
        if ($fieldRaw instanceof EmbedCustomFieldAnswer
            && $component instanceof Entry
            && method_exists($component, 'state')) {
            $component = $component->state($this->getAnswer($fieldRaw));
        }

        return $component
            ->inlineLabel()
            ->columnSpanFull();
    }

    /**
     * @template T of Component
     * @param class-string<T> $class
     * @param array<int|string, string> $ignoredOptions
     * @return T
     */
    protected function makeComponent(
        string $class,
        EmbedCustomField|EmbedCustomFieldAnswer $field,
        bool $isInfolist,
        array $ignoredOptions = []
    ): Component {
        if (!method_exists($class, 'make')) {
            throw new \RuntimeException('Make method is not defined in the component');
        }
        $component = $class::make($this->getIdentifyKey($field));

        /** @var T $component */
        return $this->modifyComponent($component, $field, $isInfolist, $ignoredOptions);
    }

    private function applyTypeOptions(
        array $typeOptions,
        Component $component,
        array $ignoredOptions,
        bool $isEntry,
        array $fieldOptions,
        array $default,
        array $generalDefault
    ): void {
        foreach ($typeOptions as $key => $typeOption) {
            if (in_array($key, $ignoredOptions, true)) {
                continue;
            }

            $value = $this->getOptionParameterWithCached(
                $key,
                false,
                $default,
                $generalDefault,
                $fieldOptions
            );

            if ($isEntry) {
                $typeOption->modifyInfolistComponent($component, $value);
            } else {
                $typeOption->modifyFormComponent($component, $value);
            }
        }
    }
}
