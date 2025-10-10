<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class ValidationAttributeOption extends TypeOption
{
    use HasOptionNoComponentModification;

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
        if (empty($value) || !method_exists($component, 'validationAttribute')) {
            return $component;
        }

        return $component->validationAttribute($value);
    }
}
