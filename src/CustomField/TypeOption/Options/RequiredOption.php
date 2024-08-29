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

    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->label($this->translate('required'))//ToDo Tranlsate
           ->columnSpanFull()
           ->live();
    }
}
