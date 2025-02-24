<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class CustomValidationAttributeOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): mixed
    {
        return null;
    }


    public function getComponent(string $name): Component
    {
        //validationAttribute
        return TextInput::make($name)
            ->label($this->translate("validation_attribute"))
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
