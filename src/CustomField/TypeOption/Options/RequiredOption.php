<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;

class RequiredOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): null {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->helperText("Benötigt") //ToDo Tranlsate
           ->label($this->translate('alpine_mask'))
           ->columnSpanFull()
           ->live();
    }
}
