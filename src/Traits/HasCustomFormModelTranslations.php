<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

trait HasCustomFormModelTranslations
{
    public static function __(string $key): string
    {
        return __('filament-package_ffhs_custom_forms::models.' . static::$translationName . '.' . $key);
    }
}
