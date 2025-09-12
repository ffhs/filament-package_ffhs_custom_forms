<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Support\Components\Component;

class FastTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    private Component $component;

    public function __construct(mixed $default, Component $component)
    {
        $this->default = $default;
        $this->component = $component;
    }

    public static function makeFast(mixed $default, Component $component): FastTypeOption
    {
        return app(static::class, ['default' => $default, 'component' => $component]);
    }

    public function getComponent(string $name): Component
    {
        return $this->component;
    }
}
