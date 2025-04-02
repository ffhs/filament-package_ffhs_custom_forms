<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

class ReorderableTypeOption extends TypeOption
{

    public function getDefaultValue(): mixed
    {
        return false;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name) //ToDo Make Custom Option out of it <=
        ->label(TypeOption::__('reorderable.label'))
            ->helperText(TypeOption::__('reorderable.helper_text'))
            ->columnSpanFull();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->reorderable($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
