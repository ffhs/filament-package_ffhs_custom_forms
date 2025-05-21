<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class DateFormatOption extends TypeOption
{

    public function getDefaultValue(): null
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('format.label'))
            ->helperText(TypeOption::__('format.helper_text'))
            ->placeholder("Y-m-d");
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->format($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
