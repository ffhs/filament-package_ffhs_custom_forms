<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

trait HasTypeOptions
{
    final public function getGeneralTypeOptionComponents(): array
    {
        return $this->getOptionsComponents($this->generalTypeOptions());
    }

    public function generalTypeOptions(): array
    {
        return [];
    }

    final public function getExtraTypeOptionComponents(): array
    {
        return $this->getOptionsComponents($this->extraTypeOptions());
    }

    public function extraTypeOptions(): array
    {
        return [];
    }

    final public function getDefaultTypeOptionValues(): array
    {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenExtraTypeOptions());
    }

    /***
     * @return array<string, TypeOption>
     */
    final public function getFlattenExtraTypeOptions(): array
    {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }

    final public function getDefaultGeneralOptionValues(): array
    {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenGeneralTypeOptions());
    }

    final public function getFlattenGeneralTypeOptions(): array
    {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }

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

    protected function getDefaultTypeOptionValuesFormArray(array $typeOptions): array
    {
        return array_map(static fn(TypeOption $extraTypeOption) => $extraTypeOption->getModifyDefault(), $typeOptions);
    }
}
