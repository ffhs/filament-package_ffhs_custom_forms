<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MinAmountOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label($this->translate("min_amount"))
                ->step(1)
                ->live()
                ->nullable()
                ->minValue(0)
                ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component; //ToDo Maby
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
