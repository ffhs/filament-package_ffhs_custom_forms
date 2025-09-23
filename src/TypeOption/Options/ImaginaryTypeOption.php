<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Hidden;
use Filament\Support\Components\Component;

class ImaginaryTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getComponent(string $name): Component
    {
        return Hidden::make($name)
            ->disabled();
    }
}
