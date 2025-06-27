<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistsComponent;

class MaxAmountOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): FormsComponent
    {
        return TextInput::make($name)
            ->label(TypeOption::__('max_amount.label'))
            ->helperText(TypeOption::__('max_amount.helper_text'))
            ->step(1)
            ->nullable()
            ->live()
            ->minValue(0)
            ->gt('min_amount')
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
