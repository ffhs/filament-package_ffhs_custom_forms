<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class ColumnSpanOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): int {
        return 3;
    }

    public function getComponent(string $name): Component {
       return TextInput::make($name)
           ->label($this->translate("column_span"))
           ->step(1)
           ->integer()
           ->minValue(1)
           ->maxValue(10)
           ->required();
    }
}
