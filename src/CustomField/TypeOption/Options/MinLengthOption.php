<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MinLengthOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): int {
        return 0;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label($this->translate("min_length"))
                ->step(1)
                ->required()
                ->integer();
    }
}
