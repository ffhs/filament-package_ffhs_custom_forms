<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class NewLineOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;
    
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
        if (!$value) {
            return $component;
        }

        return $component->columnStart(1);
    }

}
