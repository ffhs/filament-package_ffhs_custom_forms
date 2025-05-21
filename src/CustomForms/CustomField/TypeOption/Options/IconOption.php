<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfolistComponent;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{

    public function getDefaultValue(): mixed
    {
        return "";
    }

    public function getComponent(string $name): Component
    {
        return IconPicker::make($name)
            ->label(TypeOption::__('icon.label'))
            ->helperText(TypeOption::__('icon.helper_text'))
            ->columnSpanFull()
            ->columns()
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (empty($value)) return $component;
        return $component->icon($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
