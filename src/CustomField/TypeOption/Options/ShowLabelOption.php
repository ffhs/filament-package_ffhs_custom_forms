<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Component as InfolistComponent;

class ShowLabelOption extends TypeOption //toDo add to mor fields
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): bool
    {
        return true;
    }

    public function getComponent(string $name): Component
    {
        return Toggle::make($name)
            ->label($this->translate("show_label"));
    }

    public function modifyFormComponent(Component $component, mixed $value): Component
    {
        if (!$value) return $component;
        return $component->label('');
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
