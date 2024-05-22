<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ShowInViewOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): bool {
        return true;
    }

    public function getComponent(string $name): Component {
        return Toggle::make($name)
            ->label($this->translate("show_in_view"))
            ->live();
    }
}
