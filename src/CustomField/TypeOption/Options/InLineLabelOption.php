<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class InLineLabelOption extends TypeOption
{
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(): Component {
        return Toggle::make("in_line_label")
            ->label("Title in der Zeile");//ToDo Translation
    }
}
