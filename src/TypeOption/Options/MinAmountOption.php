<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinAmountOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_amount.label'))
            ->helperText(TypeOption::__('min_amount.helper_text'))
            ->step(1)
            ->live()
            ->nullable()
            ->minValue(0)
            ->integer();
    }
}
