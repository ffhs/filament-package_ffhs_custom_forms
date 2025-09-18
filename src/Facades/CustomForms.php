<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CustomFieldType getFieldTypeFromRawDate($param, CustomFormConfiguration $configuration)
 * @method static CustomFormConfiguration getFormConfiguration($custom_form_identifier)
 * @method static array getFormConfigurations()
 * @method static null|CustomForm getCustomFormFromId(int $id)
 * @method static void cacheForm(CustomForm|Collection $customForm)
 * @method static array getFormRuleTriggerClasses()
 * @method static array getFormRuleEventClasses()
 */
class CustomForms extends Facade
{
    public static function __(...$args): mixed
    {
        return __('filament-package_ffhs_custom_forms::' . implode('.', $args));
    }

    public static function config(string $string, mixed $default = null): mixed
    {
        return config('ffhs_custom_forms.' . $string);
    }

    protected static function getFacadeAccessor(): string
    {
        return \Ffhs\FilamentPackageFfhsCustomForms\CustomForms::class;
    }
}
