<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Support\Components\Component;

class ActionLabelTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('action_Label.label'))
            ->helperText(TypeOption::__('action_Label.helper_text'))
            ->columnSpanFull()
            ->live();
    }
}
