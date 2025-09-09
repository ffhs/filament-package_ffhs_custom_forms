<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

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

    public
    function modifyFormComponent(
        Component $component,
        mixed $value
    ): Component {
        if (empty($value)) {
            return $component;
        }

        return $component->validationAttribute($value);
    }

    public function modifyInfolistComponent(Component $component, mixed $value): Component
    {
        return $component;
    }
}
