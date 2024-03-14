<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MaxLenghtOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return 100;
    }

    public function getComponent(): Component {
        return
            TextInput::make("max_length")
                ->label("Maximale Länge") //ToDo Translate
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer();
    }
}
