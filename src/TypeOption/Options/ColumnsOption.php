<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class ColumnsOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 2;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('columns.label'))
            ->helperText(TypeOption::__('columns.helper_text'))
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
