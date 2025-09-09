<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class InlineOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('inline.label'))
            ->helperText(TypeOption::__('inline.helper_text'))
            ->live();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        if ($value) {
            return $component->inline();
        }

        return $component;
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
