<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Illuminate\Support\Facades\Facade;

class FilamentPackageFfhsCustomFormsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'filament-package_ffhs_custom_forms';
    }
}

