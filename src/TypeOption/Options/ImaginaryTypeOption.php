<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Hidden;
use Filament\Support\Components\Component;

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
        return Hidden::make($name)
            ->disabled();
    }
}
