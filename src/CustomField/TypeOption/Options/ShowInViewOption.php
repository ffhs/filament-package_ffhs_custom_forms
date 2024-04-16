<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ShowInViewOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return true;
    }

    public function getComponent(string $name): Component {
        return Toggle::make($name)
            ->label("Sichtbar beim Betrachten")
            ->live();
    }
}
