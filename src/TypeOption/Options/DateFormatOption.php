<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class DateFormatOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('format.label'))
            ->helperText(TypeOption::__('format.helper_text'))
            ->placeholder('Y-m-d');
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (!method_exists($component, 'format')) {
            return $component;
        }
        return $component->format($value);
    }
}
