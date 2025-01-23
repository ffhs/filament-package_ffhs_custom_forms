<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class HelptextTypeOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    public function getDefaultValue(): mixed {
        return null;
    }



    public function getComponent(string $name): Component {
        return  TextInput::make($name)
        //RichTextEditor::make($name)
            ->label($this->translate("help_text"))
            ->grow(false)
            ->columnSpanFull()
            ->live();
//            ->toolbarButtons([
//                'bold',
//                'italic',
//                'link',
//                'strike',
//                'underline',
//            ]);
    }
}
