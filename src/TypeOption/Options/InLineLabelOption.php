<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

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

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->inlineLabel($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
