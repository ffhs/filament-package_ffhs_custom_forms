<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Select;
use Filament\Support\Components\Component;

class ColorTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = 'rgb';

    public function getComponent(string $name): Component
    {
        return Select::make('color_type')
            ->label(TypeOption::__('color_type.label'))
            ->helperText(TypeOption::__('color_type.helper_text'))
            ->columnSpanFull()
            ->required()
            ->selectablePlaceholder(false)
            ->options([
                'rgb' => 'RGB',
                'hsl' => 'HSL',
                'rgba' => 'RGBA',
            ]);
    }
}
