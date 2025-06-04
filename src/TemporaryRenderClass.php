<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;

class TemporaryRenderClass
{
    use CanRenderCustomForm;

    public static function make(): static
    {
        return new static();
    }

}
