<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

class RequiredOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->label(TypeOption::__('required.label'))
            ->helperText(TypeOption::__('required.helper_text'))
            ->columnSpanFull()
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component->required($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
