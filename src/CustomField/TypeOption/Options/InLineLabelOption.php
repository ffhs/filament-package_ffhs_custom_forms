<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class InLineLabelOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): bool {
        return false;
    }

    public function getComponent(string $name): Component {
        return Toggle::make($name)
            ->label($this->translate("inline_label"));
    }
}
