<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;

class MultipleOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = false;

    public function getComponent(string $name): Component
    {
        return Toggle::make('multiple')
            ->label(TypeOption::__('multiple_uploads_allowed.label'))
            ->helperText(TypeOption::__('multiple_uploads_allowed.helper_text'))
            ->afterStateUpdated(function ($state, $set) {
                if ($state) {
                    return;
                }

                $set('reorderable', false);
            })
            ->live();
    }
}
