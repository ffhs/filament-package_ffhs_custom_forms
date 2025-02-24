<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;

class ShowTitleOption extends ShowLabelOption
{
    use TypeOptionPluginTranslate;

    public function getComponent(string $name): Component
    {
        return parent::getComponent($name)
            ->label($this->translate("show_title"));
    }
}
