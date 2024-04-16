<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MaxLengthOption extends TypeOption
{
    public function getDefaultValue(): int {
        return 100;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label("Maximale Länge") //ToDo Translate
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer();
    }
}
