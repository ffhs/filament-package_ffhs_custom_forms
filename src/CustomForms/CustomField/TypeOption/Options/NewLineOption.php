<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

class NewLineOption extends TypeOption
{

    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('new_line.label'))
            ->helperText(TypeOption::__('new_line.helper_text'));
    }


    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (!$value) return $component;
        return $component->columnStart(1);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
