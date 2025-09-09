<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;


class ColumnSpanOption extends TypeOption
{
    public function getDefaultValue(): int
    {
        return 3;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('column_span.label'))
            ->helperText(TypeOption::__('column_span.helper_text'))
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

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
