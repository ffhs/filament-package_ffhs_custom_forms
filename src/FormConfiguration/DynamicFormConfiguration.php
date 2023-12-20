<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;


use App\Domain\CustomField\CustomFieldType;

abstract class DynamicFormConfiguration
{

    public abstract function identifier(): string;

    public  static function formFieldTypes():array{
        return CustomFieldType::getAllTypes();
    }

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
