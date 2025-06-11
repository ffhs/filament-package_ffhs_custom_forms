<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CustomFieldType getFieldTypeFromRawDate($param)
 * @method static CustomFormConfiguration getFormConfiguration($custom_form_identifier)
 * @method static array getFormConfigurations()
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

