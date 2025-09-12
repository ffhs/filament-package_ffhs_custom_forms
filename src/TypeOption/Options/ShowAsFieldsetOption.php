<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class ShowAsFieldsetOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->columnSpanFull()
            ->label(TypeOption::__('show_as_fieldset.label'))
            ->helperText(TypeOption::__('show_as_fieldset.helper_text'))
            ->disabled(fn($get) => !$get('show_label'));
    }
}
