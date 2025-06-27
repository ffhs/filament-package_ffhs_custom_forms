<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

class BooleanOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->label(TypeOption::__('boolean.label'))
            ->helperText(TypeOption::__('boolean.helper_text'))
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        if ($value) {
            $component = $component->boolean();
        }

        return $component;
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
