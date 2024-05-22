<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;

class AlpineMaskOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): null {
        return null;
    }

    public function getComponent(string $name): Component {
       return TextInput::make($name)
           ->helperText( $this->translate('alpine_mask_help_text'))
           ->label($this->translate('alpine_mask'))
           ->columnSpanFull()
           ->live();
    }
}
