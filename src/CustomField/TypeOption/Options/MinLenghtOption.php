<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MinLenghtOption extends TypeOption
{
    public function getDefaultValue(): int {
        return 0;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label("Minimale Länge") //ToDo Translate
                ->step(1)
                ->required()
                ->integer();
    }
}
