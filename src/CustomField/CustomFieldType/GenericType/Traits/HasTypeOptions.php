<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;

trait HasTypeOptions
{
    public function generalTypeOptions(): array {return [];}
    public function extraTypeOptions(): array {return [];}


    public function getGeneralTypeOptionComponents(): array {
        return $this->getOptionsComponents($this->generalTypeOptions());
    }
    public function getExtraTypeOptionComponents(): array {
        return $this->getOptionsComponents($this->extraTypeOptions());
    }
    protected function getOptionsComponents(array $options): array {
        if (empty($options)) return [];
        $components = [];
        foreach ($options as $key => $option) {
            /**@var TypeOption|TypeOptionGroup $option */
            $component = $option->getModifyComponent($key);
            $components[] = $component;
        }
        return $components;
    }



    public function getFlattenExtraTypeOptions(): array {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }
    public function getFlattenGeneralTypeOptions(): array {
        return $this->getFlattenTypeOptions($this->extraTypeOptions());
    }


    public function getFlattenTypeOptions(array $typeOptions): array {
        $options = [];
        foreach ($typeOptions as $key => $extraTypeOption) {
            if($extraTypeOption instanceof TypeOptionGroup){
                $options = array_merge($options, $extraTypeOption->getTypeOptions());
                continue;
            }
            /**@var TypeOption $extraTypeOption */
            $options[$key] = $extraTypeOption->getModifyDefault();
        }
        return $options;
    }

    public function getDefaultTypeOptionValues(): array {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenExtraTypeOptions());
    }
    public function getDefaultGeneralOptionValues(): array {
        return $this->getDefaultTypeOptionValuesFormArray($this->getFlattenGeneralTypeOptions());
    }



    protected function getDefaultTypeOptionValuesFormArray(array $typeOptions): array {
        $defaults = [];
        foreach ($typeOptions as $key => $extraTypeOption) {
            /**@var TypeOption $extraTypeOption */
            $defaults[$key] = $extraTypeOption->getModifyDefault();
        }
        return $defaults;
    }
}
