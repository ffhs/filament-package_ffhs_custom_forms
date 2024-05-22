<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ShowTitleOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return true;
    }

    public function getComponent(string $name): Component {
        return  Toggle::make($name)
            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_title"))
            ->live();
    }
}
