<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class InLineLabelOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('inline_label.label'))
            ->helperText(TypeOption::__('inline_label.helper_text'));
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component->inlineLabel($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
