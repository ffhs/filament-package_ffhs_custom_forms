<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;


use Filament\Support\Components\Component;

trait HasOptionNoComponentModification
{
    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component;
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
