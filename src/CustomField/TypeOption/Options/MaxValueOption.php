<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MaxValueOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): int {
        return 100;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label($this->translate("max_value"))
                ->step(1)
                ->required()
                ->integer();
    }
}
