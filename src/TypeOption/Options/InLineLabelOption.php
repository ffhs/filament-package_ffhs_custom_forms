<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class InLineLabelOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'inlineLabel';
    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('inline_label.label'))
            ->helperText(TypeOption::__('inline_label.helper_text'));
    }
}
