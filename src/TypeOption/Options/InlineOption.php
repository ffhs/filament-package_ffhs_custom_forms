<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class InlineOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label(TypeOption::__('inline.label'))
            ->helperText(TypeOption::__('inline.helper_text'))
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if ($value && method_exists($component, 'inline')) {
            return $component->inline();
        }

        return $component;
    }
}
