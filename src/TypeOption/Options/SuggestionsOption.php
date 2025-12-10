<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class SuggestionsOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = [];

    public function getComponent(string $name): Component
    {
        return Group::make()
            ->statePath($name)
            ->columnSpanFull()
            ->schema(fn($record) => once(fn() => [
                Repeater::make(app()->getLocale())
                    ->helperText(TypeOption::__('suggestions.helper_text'))
                    ->addActionLabel(TypeOption::__('suggestions.add_label'))
                    ->schema([TextInput::make('value')]) //ToDO change to simple repeater
                    ->hiddenLabel(),
            ]));
    }

}
