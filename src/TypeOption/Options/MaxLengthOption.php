<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MaxLengthOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 100;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('max_length.label'))
            ->helperText(TypeOption::__('max_length.helper_text'))
            ->columnStart(1)
            ->step(1)
            ->required()
            ->integer();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component->maxLength($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
