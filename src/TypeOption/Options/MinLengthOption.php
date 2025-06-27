<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistsComponent;

class MinLengthOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 0;
    }

    public function getComponent(string $name): FormsComponent
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_length.label'))
            ->helperText(TypeOption::__('min_length.helper_text'))
            ->step(1)
            ->required()
            ->integer();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component->minLength($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
