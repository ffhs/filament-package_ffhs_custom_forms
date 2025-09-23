<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class MaxSelectOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = 0;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->hidden(fn($get) => !$get('several'))
            ->label(TypeOption::__('max_select.label'))
            ->helperText(TypeOption::__('max_select.helper_text'))
            ->minValue(0)
            ->step(1)
            ->numeric();
    }
}
