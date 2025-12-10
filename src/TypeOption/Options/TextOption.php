<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use App;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Components\Component;

class TextOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = '';

    public function getComponent(string $name): Component
    {
        $buttons = [
            'bold',
            'bulletList',
            'italic',
            'link',
            'orderedList',
            'underline',
        ];

        return RichEditor::make($name . '.' . App::getLocale())
            ->label(TypeOption::__('text.label'))
            ->helperText(TypeOption::__('text.helper_text'))
            ->columnSpanFull()
            ->toolbarButtons(
                $buttons
            );
    }
}
