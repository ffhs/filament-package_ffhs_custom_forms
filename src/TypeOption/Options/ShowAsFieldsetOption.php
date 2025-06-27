<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

class ShowAsFieldsetOption extends TypeOption
{

    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->columnSpanFull()
            ->label(TypeOption::__('show_as_fieldset.label'))
            ->helperText(TypeOption::__('show_as_fieldset.helper_text'))
            ->disabled(fn($get) => !$get('show_label'));
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component;
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
