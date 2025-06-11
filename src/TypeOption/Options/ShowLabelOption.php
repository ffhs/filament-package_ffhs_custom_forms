<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

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

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if ($value) {
            return $component->label('');
        }
        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
