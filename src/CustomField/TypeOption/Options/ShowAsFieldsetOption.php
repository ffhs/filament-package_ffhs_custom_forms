<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ShowAsFieldsetOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
       return Toggle::make($name)
           ->columnSpanFull()
           ->label("Als Fieldset beim Betrachten anzeigen") //ToDo Translate,
           ->disabled(fn($get) => !$get("show_title"));
    }
}
