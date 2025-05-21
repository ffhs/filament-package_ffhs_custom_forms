<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

class ShowLabelOption extends TypeOption //toDo add to more fields
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
        if (!$value) return $component;
        return $component->label('');
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
