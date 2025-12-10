<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MinSelectOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = 0;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('min_select.label'))
            ->helperText(TypeOption::__('min_select.helper_text'))
            ->hidden(fn($get) => !$get('several'))
            ->columnStart(1)
            ->minValue(0)
            ->numeric()
            ->step(1);
    }
}
