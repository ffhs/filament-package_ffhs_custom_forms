<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class MinAmountOption extends TypeOption
{

    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return
            TextInput::make($name)
                ->label(TypeOption::__('min_amount.label'))
                ->helperText(TypeOption::__('min_amount.helper_text'))
                ->step(1)
                ->live()
                ->nullable()
                ->minValue(0)
                ->integer();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component; //ToDo Maby
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
