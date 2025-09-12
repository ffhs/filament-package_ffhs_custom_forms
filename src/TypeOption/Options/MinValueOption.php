<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinValueOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'minValue';
    protected mixed $default = 100;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_value.label'))
            ->helperText(TypeOption::__('min_value.helper_text'))
            ->step(1)
            ->required()
            ->integer();
    }
}
