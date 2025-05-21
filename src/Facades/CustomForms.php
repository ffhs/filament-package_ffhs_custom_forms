<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Facades;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\DataManagment\HasCustomFormSaveDataManagement;
use Illuminate\Support\Facades\Facade;

class CustomForms extends Facade
{
    use HasCustomFormSaveDataManagement;

    protected static function getFacadeAccessor()
    {
        return \Ffhs\FilamentWorkflows\FilamentWorkflows::class;
    }

}

