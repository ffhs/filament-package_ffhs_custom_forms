<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Illuminate\Support\HtmlString;

class HelperTextTypeOption extends TypeOption
{
    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): FormsComponent
    {
        return TextInput::make($name)
            ->label(TypeOption::__('helper_text.label'))
            ->helperText(TypeOption::__('helper_text.helper_text'))
            ->grow(false)
            ->columnSpanFull()
            ->nullable()
            ->live();
    }

    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        if (!empty($value)) {
            $text = str($value)->sanitizeHtml();
            $text = new HtmlString($text);
            $component = $component->helperText($text);
        }

        return $component;
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }
}
