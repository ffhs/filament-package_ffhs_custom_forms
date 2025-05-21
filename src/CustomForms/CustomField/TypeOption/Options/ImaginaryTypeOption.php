<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;

class ImaginaryTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    private mixed $defaultValue;

    public function default(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getComponent(string $name): Component
    {
        return Hidden::make($name)->disabled();
    }
}
