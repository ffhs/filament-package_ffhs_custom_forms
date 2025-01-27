<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\DataManagment\HasCustomFormSaveDataManagement;
use Illuminate\Support\Facades\Facade;

class CustomForms extends Facade
{


    use HasCustomFormSaveDataManagement;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'filament-package_ffhs_custom_forms';
    }
}

