<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return '';
    }

    public function getComponent(string $name): FormsComponent
    {
        return IconPicker::make($name)
            ->label(TypeOption::__('icon.label'))
            ->helperText(TypeOption::__('icon.helper_text'))
            ->columnSpanFull()
            ->columns()
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        if (empty($value)) {
            return $component;
        }

        return $component->icon($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
