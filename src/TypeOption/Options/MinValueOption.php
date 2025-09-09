<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinValueOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 100;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_value.label'))
            ->helperText(TypeOption::__('min_value.helper_text'))
            ->step(1)
            ->required()
            ->integer();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component->minValue($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
