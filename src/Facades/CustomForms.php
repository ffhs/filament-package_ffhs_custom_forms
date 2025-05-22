<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CustomFieldType getFieldTypeFromRawDate($param)
 * @method static Collection getAllowedGeneralFieldsInFormIdentifier(string $formIdentifier)
 */
class CustomForms extends Facade
{
    public static function __(...$args): string
    {
        return __('filament-package_ffhs_custom_forms::' . implode('.', $args));
    }

    public static function config(string $string, mixed $default = null): mixed
    {
        return config('ffhs_custom_forms.' . $string);
    }


    protected static function getFacadeAccessor()
    {
        return \Ffhs\FilamentPackageFfhsCustomForms\CustomForms::class;
    }
}

