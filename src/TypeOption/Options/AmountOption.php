<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class AmountOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = 1;

    public function getComponent(string $name): Component
    {
        return TextInput::make('amount')
            ->label(TypeOption::__('size.label'))
            ->helperText(TypeOption::__('size.helper_text'))
            ->columnStart(1)
            ->minValue(1)
            ->required()
            ->numeric();
    }
}
