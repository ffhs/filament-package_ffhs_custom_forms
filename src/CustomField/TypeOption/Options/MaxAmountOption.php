<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MaxAmountOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): mixed {
        return null;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label($this->translate("max_amount"))
                ->step(1)
                ->nullable()
                ->live()
                ->minValue(0)
                ->gt("min_amount")
                ->integer();
    }
}
