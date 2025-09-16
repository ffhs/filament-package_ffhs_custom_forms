<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class HelperTextTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('helper_text.label'))
            ->helperText(TypeOption::__('helper_text.helper_text'))
            ->grow(false)
            ->columnSpanFull()
            ->nullable()
            ->live();
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
}
