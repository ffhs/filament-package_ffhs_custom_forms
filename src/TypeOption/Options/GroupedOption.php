<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class GroupedOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make('grouped')
            ->helperText(TypeOption::__('toggle_grouped.helper_text'))
            ->label(TypeOption::__('toggle_grouped.label'))
            ->disabled(fn($get) => $get('inline'))
            ->live();
    }
}
