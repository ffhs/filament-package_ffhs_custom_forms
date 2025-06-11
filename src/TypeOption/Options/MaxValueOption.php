<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MaxValueOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 100;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label(TypeOption::__('max_value.label'))
                ->helperText(TypeOption::__('max_value.helper_text'))
                ->step(1)
                ->required()
                ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->maxValue($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
