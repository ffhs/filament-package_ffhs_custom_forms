<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;

trait HasCustomTypePackageTranslation
{
    public static function __(string $string): string
    {
        return CustomForms::__('custom_field_types.' . static::identifier() . '.' . $string);
    }

    public function getTranslatedName(): string
    {
        return static::__('label');
    }
}
