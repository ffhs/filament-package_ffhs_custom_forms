<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;
use Filament\Support\Components\Component;

trait HasTypeOptions
{
    /**
     * @return array<int|string, Component>
     */
    final public function getGeneralTypeOptionComponents(): array
    {
        return once(fn(): array => $this->getOptionsComponents($this->generalTypeOptions()));
    }

    /**
     * @return array<int|string, TypeOption|TypeOptionGroup>
     */
    public function generalTypeOptions(): array
    {
        return [];
    }

    /**
     * @return array<int|string, Component>
     */
    final public function getExtraTypeOptionComponents(): array
    {
        return once(fn(): array => $this->getOptionsComponents($this->extraTypeOptions()));
    }

    /**
     * @return array<int|string, TypeOption|TypeOptionGroup>
     */
    public function extraTypeOptions(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    final public function getDefaultTypeOptionValues(): array
    {
        $flattenExtraTypes = $this->getFlattenExtraTypeOptions();
        return $this->getDefaultTypeOptionValuesFormArray($flattenExtraTypes);
    }

    /**
     * @return array<string, TypeOption>
     */
    final public function getFlattenExtraTypeOptions(): array
    {
        $flattenExtraTypes = $this->extraTypeOptions(); //May Optimize more
        return $this->getFlattenTypeOptions($flattenExtraTypes);
    }

    /**
     * @return array<string, mixed>
     */
    final public function getDefaultGeneralOptionValues(): array
    {
        $stuff = $this->getFlattenGeneralTypeOptions();
        return $this->getDefaultTypeOptionValuesFormArray($stuff);
    }

    /**
     * @return array<string, TypeOption>
     */
    final public function getFlattenGeneralTypeOptions(): array
    {
        return once(fn(): array => $this->getFlattenTypeOptions($this->generalTypeOptions()));
    }

    /**
     * @param array<int|string, TypeOption|TypeOptionGroup> $typeOptions
     * @return array<string, TypeOption>
     */
    public function getFlattenTypeOptions(array $typeOptions): array
    {
        $options = [];

        foreach ($typeOptions as $key => $extraTypeOption) {
            if ($extraTypeOption instanceof TypeOptionGroup) {
                $options = [
                    ...$options,
                    ...$extraTypeOption->getTypeOptions()
                ];
                continue;
            }

            /**@var TypeOption $extraTypeOption */
            $options[$key] = $extraTypeOption;
        }

        return $options;
    }

    /**
     * @param array<string, TypeOption|TypeOptionGroup> $options
     * @return array<int|string, Component>
     */
    protected function getOptionsComponents(array $options): array
    {
        if (empty($options)) {
            return [];
        }

        $components = [];

        foreach ($options as $key => $option) {
            /**@var TypeOption|TypeOptionGroup $option */
            $component = $option->getModifyOptionComponent($key);
            $components[] = $component;
        }

        return $components;
    }

    /**
     * @param array<string, TypeOption|TypeOptionGroup> $typeOptions
     * @return array<string, mixed>
     */
    protected function getDefaultTypeOptionValuesFormArray(array $typeOptions): array
    {
        return array_map(static fn(TypeOption $extraTypeOption) => $extraTypeOption->getModifyDefault(), $typeOptions);
    }
}
