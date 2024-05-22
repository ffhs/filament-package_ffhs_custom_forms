<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MinValueOption extends TypeOption
{
    public function getDefaultValue(): int {
        return 100;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_value"))
                ->step(1)
                ->required()
                ->integer();
    }
}
