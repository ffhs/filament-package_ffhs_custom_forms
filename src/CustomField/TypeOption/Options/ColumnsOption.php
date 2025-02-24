<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class ColumnsOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): int
    {
        return 2;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label($this->translate('columns_count'))
            ->maxValue(10)
            ->minValue(1)
            ->step(1)
            ->required()
            ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->columns($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component; //ToDo May Improve
    }
}
