<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

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

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component->columns($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component; //ToDo May Improve
    }
}
