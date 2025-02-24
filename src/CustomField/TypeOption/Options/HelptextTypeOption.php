<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;
use Illuminate\Support\HtmlString;

class HelptextTypeOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            //RichTextEditor::make($name) //ToDo maby change back?
            ->label($this->translate("help_text"))
            ->grow(false)
            ->columnSpanFull()
            ->nullable()
            ->live();
//            ->toolbarButtons([
//                'bold',
//                'italic',
//                'link',
//                'strike',
//                'underline',
//            ]);
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        return $component->helperText($value ? new HtmlString($value) : null);
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
