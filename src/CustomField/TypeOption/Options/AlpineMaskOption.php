<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;

class AlpineMaskOption extends TypeOption
{
    public function getDefaultValue(): null {
        return null;
    }

    public function getComponent(string $name): Component {
       return TextInput::make($name)
           ->label("Alpine Maske") //ToDo Translate
           ->live();
    }
}
