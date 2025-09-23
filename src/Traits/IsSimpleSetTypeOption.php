<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Support\Components\Component;

trait IsSimpleSetTypeOption
{
    use HasOptionNoComponentModification;

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        $attribute = $this->attribute;
        if (method_exists($component, $this->attribute)) {
            return $component->$attribute($value);
        }
        return $component;
    }
}
