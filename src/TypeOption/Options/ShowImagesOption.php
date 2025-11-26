<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class ShowImagesOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make('show_images')
            ->label(TypeOption::__('show_images.label'))
            ->helperText(TypeOption::__('show_images.helper_text'))
            ->disabled(fn($get) => !$get('image'))
            ->hidden(fn($get) => !$get('image'))
            ->live();
    }
}
