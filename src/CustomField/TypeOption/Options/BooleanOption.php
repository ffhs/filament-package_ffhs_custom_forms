<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class BooleanOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.boolean"))
           ->live();
    }
}
