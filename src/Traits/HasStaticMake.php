<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

trait HasStaticMake
{
    public static function make(): static
    {
        return app(static::class);
    }
}
