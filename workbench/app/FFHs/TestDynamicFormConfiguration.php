<?php

namespace Workbench\App\FFHs;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;

class TestDynamicFormConfiguration extends DynamicFormConfiguration
{

    public static function identifier(): string
    {
        return 'test_form';
    }

    public static function displayName(): string
    {
        return 'test form';
    }
}
