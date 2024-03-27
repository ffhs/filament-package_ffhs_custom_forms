<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class BooleanOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->label("Ja/Nein Feld") //ToDo Translate
           ->live();
    }
}
