<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsSimpleSetTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class ReorderableTypeOption extends TypeOption
{
    use IsSimpleSetTypeOption;

    protected string $attribute = 'reorderable';
    protected mixed $default = false;

    public function getDefaultValue(): mixed
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('reorderable.label'))
            ->helperText(TypeOption::__('reorderable.helper_text'))
            ->columnSpanFull();
    }
}
