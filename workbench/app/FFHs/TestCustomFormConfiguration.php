<?php

namespace Workbench\App\FFHs;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;

class TestCustomFormConfiguration extends CustomFormConfiguration
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
