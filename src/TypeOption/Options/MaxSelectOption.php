<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MaxSelectOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 0;
    }

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

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
