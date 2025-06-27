<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

//toDo add Option to more FieldTypes
class ShowInViewOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return true;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->label(TypeOption::__('show_in_view.label'))
            ->helperText(TypeOption::__('show_in_view.helper_text'))
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component;
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component->visible($value);
    }
}
