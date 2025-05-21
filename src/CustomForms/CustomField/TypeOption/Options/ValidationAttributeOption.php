<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class ValidationAttributeOption extends TypeOption
{


    public function getDefaultValue(): mixed
    {
        return null;
    }


    public function getComponent(string $name): Component
    {
        //validationAttribute
        return TextInput::make($name)
            ->label(TypeOption::__('validation_attribute.label'))
            ->helperText(TypeOption::__('validation_attribute.helper_text'))
            ->columnSpanFull()
            ->nullable()
            ->live();
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (empty($value)) return $component;
        return $component->validationAttribute($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
