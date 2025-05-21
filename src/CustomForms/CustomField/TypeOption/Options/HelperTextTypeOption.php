<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistComponent;
use Illuminate\Support\HtmlString;

class HelperTextTypeOption extends TypeOption
{

    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            //RichTextEditor::make($name) //ToDo maby change back?
            ->label(TypeOption::__('helper_text.label'))
            ->helperText(TypeOption::__('helper_text.helper_text'))
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
        if (!empty($value)) {
            $text = str($value)->sanitizeHtml();
            $text = new HtmlString($text);
            $component = $component->helperText($text);
        }

        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
