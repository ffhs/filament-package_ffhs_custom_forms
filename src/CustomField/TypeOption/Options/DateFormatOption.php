<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class DateFormatOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): null {
        return null;
    }

    public function getComponent(string $name): Component {
       return TextInput::make($name)
           ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.format"))
           ->placeholder("Y-m-d");
    }
}
