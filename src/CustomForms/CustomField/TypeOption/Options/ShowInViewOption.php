<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

class ShowInViewOption extends TypeOption
{

    public function getDefaultValue(): bool
    {
        return true;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name) //ToDo add in more Fields
        ->label(TypeOption::__('show_in_view.label'))
            ->helperText(TypeOption::__('show_in_view.helper_text'))
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component->visible($value);
    }
}
