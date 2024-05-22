<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Guava\FilamentIconPicker\Forms\IconPicker;

class IconOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): mixed {
        return "";
    }

    public function getComponent(string $name): Component {
       return IconPicker::make($name)
           ->label($this->translate("icon"))
           ->columnSpanFull()
           ->columns()
           ->live();
    }
}
