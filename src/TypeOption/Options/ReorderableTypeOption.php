<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistsComponent;

class ReorderableTypeOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return false;
    }

    public function getComponent(string $name): FormsComponent
    {
        return Toggle::make($name)
            ->label(TypeOption::__('reorderable.label'))
            ->helperText(TypeOption::__('reorderable.helper_text'))
            ->columnSpanFull();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component->reorderable($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
