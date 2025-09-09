<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinAmountOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_amount.label'))
            ->helperText(TypeOption::__('min_amount.helper_text'))
            ->step(1)
            ->live()
            ->nullable()
            ->minValue(0)
            ->integer();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component;
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
