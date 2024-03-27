<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class InlineOption extends TypeOption
{
    public function getDefaultValue(): int {
        return 4;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->label("In einer Zeile")//ToDo Translate
           ->live();
    }
}
