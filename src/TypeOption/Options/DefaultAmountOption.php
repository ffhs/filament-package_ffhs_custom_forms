<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class DefaultAmountOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = 1;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->helperText(TypeOption::__('default_amount.helper_text'))
            ->label(TypeOption::__('default_amount.label'))
            ->lte('max_amount')
            ->minValue(0)
            ->integer()
            ->required();
    }
}
