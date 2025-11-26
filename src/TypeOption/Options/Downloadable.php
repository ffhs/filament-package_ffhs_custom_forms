<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class Downloadable extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = true;

    public function getComponent(string $name): Component
    {
        return Toggle::make('downloadable')
            ->label(TypeOption::__('downloadable.label'))
            ->helperText(TypeOption::__('downloadable.helper_text'));
    }
}
