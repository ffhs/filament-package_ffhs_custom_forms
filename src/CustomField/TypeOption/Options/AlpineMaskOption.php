<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class AlpineMaskOption extends TypeOption
{


    public function getDefaultValue(): null
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('alpine_mask.label'))
            ->helperText(TypeOption::__('alpine_mask.helper_text'))
            ->columnSpanFull()
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (!empty($value)) return $component->mask($value);
        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
