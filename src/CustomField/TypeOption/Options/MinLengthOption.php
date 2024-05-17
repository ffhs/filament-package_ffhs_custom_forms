<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class MinLengthOption extends TypeOption
{
    public function getDefaultValue(): int {
        return 0;
    }

    public function getComponent(string $name): Component {
        return
            TextInput::make($name)
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_length"))
                ->step(1)
                ->required()
                ->integer();
    }
}
