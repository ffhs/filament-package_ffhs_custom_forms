<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

class InLineLabelOption extends TypeOption
{
    public function getDefaultValue(): bool
    {
        return false;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->label(TypeOption::__('inline_label.label'))
            ->helperText(TypeOption::__('inline_label.helper_text'));
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component->inlineLabel($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
