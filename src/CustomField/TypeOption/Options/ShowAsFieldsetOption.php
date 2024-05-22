<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ShowAsFieldsetOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->columnSpanFull()
           ->label($this->translate("show_as_fieldset"))
           ->disabled(fn($get) => !$get("show_title"));
    }
}
