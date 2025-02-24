<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class ColumnSpanOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): int
    {
        return 3;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label($this->translate("column_span"))
            ->step(1)
            ->integer()
            ->minValue(1)
            ->maxValue(10)
            ->required();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->columnSpan($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
