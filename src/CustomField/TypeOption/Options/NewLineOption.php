<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class NewLineOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(): Component {
        return Toggle::make("new_line_option")
            ->label("Neue Zeile");//ToDo Translation
    }
}
