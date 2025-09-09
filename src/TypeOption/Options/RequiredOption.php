<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class RequiredOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('required.label'))
            ->helperText(TypeOption::__('required.helper_text'))
            ->columnSpanFull()
            ->live();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component->required($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
