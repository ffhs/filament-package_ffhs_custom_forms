<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Filament\Forms\Components\Component;

class FastTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    private mixed $defaultValue;
    private Component $component;

    public function __construct(mixed $defaultValue, Component $component)
    {
        $this->defaultValue = $defaultValue;
        $this->component = $component;
    }

    public static function makeFast(mixed $defaultValue, Component $component): FastTypeOption
    {
        return new FastTypeOption($defaultValue, $component);
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getComponent(string $name): Component
    {
        return $this->component;
    }
}
