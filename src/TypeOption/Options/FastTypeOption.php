<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Support\Components\Component;

class FastTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    private Component|Closure $component;

    public function __construct(mixed $default, Component|Closure $component)
    {
        $this->default = $default;
        $this->component = $component;
    }

    public static function makeFast(mixed $default, Component|Closure $component): FastTypeOption
    {
        return app(static::class, ['default' => $default, 'component' => $component]);
    }

    public function getComponent(string $name): Component
    {
        if ($this->component instanceof Closure) {
            return ($this->component)($name);
        }

        return $this->component;
    }
}
