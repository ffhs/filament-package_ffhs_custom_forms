<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MinLengthOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 0;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label(TypeOption::__('min_length.label'))
                ->helperText(TypeOption::__('min_length.helper_text'))
                ->step(1)
                ->required()
                ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->minLength($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
