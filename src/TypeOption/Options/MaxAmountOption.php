<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MaxAmountOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'maxValue';

    public function getComponent(string $name): Component
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
}
