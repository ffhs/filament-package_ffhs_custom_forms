<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Support\Components\Component;
use Guava\IconPicker\Forms\Components\IconPicker;

class IconOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = '';

    public function getComponent(string $name): Component
    {
        return IconPicker::make($name)
            ->label(TypeOption::__('icon.label'))
            ->helperText(TypeOption::__('icon.helper_text'))
            ->columnSpanFull()
            ->columns()
            ->live();
    }

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        if (empty($value)) {
            return $component;
        }

        return $component->icon($value);
    }
}
