<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CustomFieldType getFieldTypeFromRawDate($param)
 */
class CustomForms extends Facade
{
    public static function __(...$args): string
    {
        return __('filament-package_ffhs_custom_forms::' . implode('.', $args));
    }

    protected static function getFacadeAccessor()
    {
        return \Ffhs\FilamentPackageFfhsCustomForms\CustomForms::class;
    }
}

