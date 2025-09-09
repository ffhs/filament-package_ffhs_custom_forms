<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return '';
    }

    public function getComponent(string $name): Component
    {
        return TextEntry::make($name); //ToDo Reimplement
//        return IconPicker::make($name)
//            ->label(TypeOption::__('icon.label'))
//            ->helperText(TypeOption::__('icon.helper_text'))
//            ->columnSpanFull()
//            ->columns()
//            ->live();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        if (empty($value)) {
            return $component;
        }

        return $component->icon($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
