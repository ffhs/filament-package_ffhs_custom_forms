<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;

class DateFormatOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): null
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.format"))
            ->placeholder("Y-m-d");
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->format($value);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
