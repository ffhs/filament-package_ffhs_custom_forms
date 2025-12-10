<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class ShowAsLinkOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = true;

    public function getComponent(string $name): Component
    {
        return Toggle::make('show_as_link')
            ->label(TypeOption::__('show_as_link.label'))
            ->helperText(TypeOption::__('show_as_link.helper_text'));
    }
}
