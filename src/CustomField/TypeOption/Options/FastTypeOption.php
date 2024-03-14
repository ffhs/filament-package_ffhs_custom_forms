<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;

class FastTypeOption extends TypeOption
{

    private mixed $defaultValue;
    private Component $component;

    public function __construct(mixed $defaultValue, Component $component) {
        $this->defaultValue = $defaultValue;
        $this->component = $component;
    }

    public function getDefaultValue(): int {
        return   $this->defaultValue;
    }

    public function getComponent(): Component {
        return $this->component;
    }
}
