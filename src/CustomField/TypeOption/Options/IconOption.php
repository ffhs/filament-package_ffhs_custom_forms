<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{
    public function getDefaultValue(): mixed {
        return "";
    }

    public function getComponent(string $name): Component {
       return IconPicker::make($name)
           ->label("Icon") //ToDo Translate#
           ->columnSpanFull()
           ->columns()
           ->live();
    }
}
