<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistsComponent;

class ValidationAttributeOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): FormsComponent
    {
        //validationAttribute
        return TextInput::make($name)
            ->label(TypeOption::__('validation_attribute.label'))
            ->helperText(TypeOption::__('validation_attribute.helper_text'))
            ->columnSpanFull()
            ->nullable()
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        if (empty($value)) {
            return $component;
        }

        return $component->validationAttribute($value);
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
