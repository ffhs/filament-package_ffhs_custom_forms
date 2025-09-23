<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MaxValueOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'maxValue';
    protected mixed $default = 100;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('max_value.label'))
            ->helperText(TypeOption::__('max_value.helper_text'))
            ->required()
            ->integer()
            ->step(1);
    }
}
