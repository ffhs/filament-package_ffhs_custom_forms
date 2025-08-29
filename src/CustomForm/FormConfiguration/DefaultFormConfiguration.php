<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration;

class DefaultFormConfiguration extends CustomFormConfiguration
{
    public static function displayName(): string
    {
        return 'Default';
    }

    public static function identifier(): string
    {
        return 'default';
    }
}
