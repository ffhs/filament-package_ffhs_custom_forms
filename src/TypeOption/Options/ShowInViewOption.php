<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

//toDo add Option to more FieldTypes
class ShowInViewOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return true;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('show_in_view.label'))
            ->helperText(TypeOption::__('show_in_view.helper_text'))
            ->live();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        return $component;
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component->visible($value);
    }
}
