<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

//toDo add Option to more FieldTypes
class ShowLabelOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return true;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('show_label.label'))
            ->helperText(TypeOption::__('show_label.helper_text'));
    }

    protected function modifyComponent(Component $component, mixed $value): Component
    {
        if (method_exists($component, 'hiddenLabel')) {
            $component->hiddenLabel(!$value);
        }
        return $component;
    }
}
