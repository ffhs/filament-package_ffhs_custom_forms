<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array loadCustomFormEditorData(\Illuminate\Database\Eloquent\Model $getRecord)
 * @method static array loadEditorField(\Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField $customField)
 */
class CustomForms extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \Ffhs\FilamentPackageFfhsCustomForms\CustomForms::class;
    }

}

