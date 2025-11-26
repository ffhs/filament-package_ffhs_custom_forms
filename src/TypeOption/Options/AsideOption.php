<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class AsideOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make('aside')
            ->label(TypeOption::__('aside.label'))
            ->helperText(TypeOption::__('aside.helper_text'))
            ->disabled(fn($get) => !$get('show_label'));
    }
}
