<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;

trait HasTypeOptions
{
    public final function getGeneralTypeOptionComponents(): array
    {
        return $this->getOptionsComponents($this->generalTypeOptions());
    }

    protected function getOptionsComponents(array $options): array
    {
        if (empty($options)) return [];
        $components = [];
        foreach ($options as $key => $option) {
            /**@var TypeOption|TypeOptionGroup $option */
            $component = $option->getModifyOptionComponent($key);
            $components[] = $component;
        }
        return $components;
    }

    public function generalTypeOptions(): array
    {
        return [];
    }

    public final function getExtraTypeOptionComponents(): array
    {
        return $this->getOptionsComponents($this->extraTypeOptions());
    }

    public function extraTypeOptions(): array
    {
        return [];
    }

    public final function getDefaultTypeOptionValues(): array
    {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenExtraTypeOptions());
    }

    protected function getDefaultTypeOptionValuesFormArray(array $typeOptions): array
    {
        $defaults = [];
        foreach ($typeOptions as $key => $extraTypeOption) {
            /**@var TypeOption $extraTypeOption */
            $defaults[$key] = $extraTypeOption->getModifyDefault();
        }
        return $defaults;
    }

    /***
     * @return array<string, TypeOption>
     */
    public final function getFlattenExtraTypeOptions(): array
    {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }

    public function getFlattenTypeOptions(array $typeOptions): array
    {
        $options = [];
        foreach ($typeOptions as $key => $extraTypeOption) {
            if ($extraTypeOption instanceof TypeOptionGroup) {
                $options = array_merge($options, $extraTypeOption->getTypeOptions());
                continue;
            }
            /**@var TypeOption $extraTypeOption */
            $options[$key] = $extraTypeOption;
        }
        return $options;
    }

    public final function getDefaultGeneralOptionValues(): array
    {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenGeneralTypeOptions());
    }

    public final function getFlattenGeneralTypeOptions(): array
    {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }
}
