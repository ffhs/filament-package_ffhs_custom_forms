<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array loadCustomFormEditorData(Model $getRecord)
 * @method static array loadEditorField(CustomField $customField)
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

