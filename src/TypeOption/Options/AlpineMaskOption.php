<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class AlpineMaskOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('alpine_mask.label'))
            ->helperText(TypeOption::__('alpine_mask.helper_text'))
            ->columnSpanFull()
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (empty($value) || !method_exists($component, 'mask')) {
            return $component;
        }

        return $component->mask($value);
    }
}
