<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;


abstract class DynamicFormConfiguration
{
    public abstract static function formFieldTypes():array;

    public abstract function identifyer(): string;

    public static function displayViewMode():string {
        return 'default';
    }
    public static function displayEditMode():string {
        return 'default';
    }
    public static function displayCreateMode():string {
        return 'default';
    }

}
