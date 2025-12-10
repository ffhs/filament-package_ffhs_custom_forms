<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class SeveralOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make('several')
            ->label(TypeOption::__('several.label'))
            ->helperText(TypeOption::__('several.helper_text'))
            ->columnSpanFull()
            ->live();
    }
}
