<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class RequiredOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'required';
    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('required.label'))
            ->helperText(TypeOption::__('required.helper_text'))
            ->columnSpanFull()
            ->live();
    }
}
