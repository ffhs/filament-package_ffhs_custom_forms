<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfolistComponent;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): mixed
    {
        return "";
    }

    public function getComponent(string $name): Component
    {
        return IconPicker::make($name)
            ->label($this->translate("icon"))
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
