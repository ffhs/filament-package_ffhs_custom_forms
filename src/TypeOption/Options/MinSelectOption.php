<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MinSelectOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 0;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label(TypeOption::__('min_select.label'))
                ->helperText(TypeOption::__('min_select.helper_text'))
                ->hidden(fn($get) => !$get('several'))
                ->columnStart(1)
                ->minValue(0)
                ->numeric()
                ->step(1);
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
