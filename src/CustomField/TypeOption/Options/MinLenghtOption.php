<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MinLenghtOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return 0;
    }

    public function getComponent(): Component {
        return
            TextInput::make("min_length")
                ->label("Minimale Länge") //ToDo Translate
                ->step(1)
                ->required()
                ->integer();
    }
}
