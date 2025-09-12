<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinLengthOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'minLength';
    protected mixed $default = 0;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_length.label'))
            ->helperText(TypeOption::__('min_length.helper_text'))
            ->step(1)
            ->required()
            ->integer();
    }
}
