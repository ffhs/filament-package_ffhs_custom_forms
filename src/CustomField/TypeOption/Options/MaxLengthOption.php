<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MaxLengthOption extends TypeOption
{


    public function getDefaultValue(): int
    {
        return 100;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label(TypeOption::__('max_length.label'))
                ->helperText(TypeOption::__('max_length.helper_text'))
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->maxLength($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
