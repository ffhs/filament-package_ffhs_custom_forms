<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MaxValueOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return 100;
    }

    public function getComponent(): Component {
        return
            TextInput::make("max_value")
                ->label("Maximale Grösse") //ToDo Translate
                ->step(1)
                ->required()
                ->integer();
    }
}
