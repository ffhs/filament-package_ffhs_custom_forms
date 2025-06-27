<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistsComponent;

class MaxValueOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 100;
    }

    public function getComponent(string $name): FormsComponent
    {
        return TextInput::make($name)
            ->label(TypeOption::__('max_value.label'))
            ->helperText(TypeOption::__('max_value.helper_text'))
            ->step(1)
            ->required()
            ->integer();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component->maxValue($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
