<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class ActionLabelTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;


    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return TextInput::make($name)
            ->label(TypeOption::__('action_Label.label'))
            ->helperText(TypeOption::__('action_Label.helper_text'))
            ->columnSpanFull()
            ->live();
    }
}
