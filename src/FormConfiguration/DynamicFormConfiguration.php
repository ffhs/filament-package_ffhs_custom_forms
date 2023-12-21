<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;



use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

abstract class DynamicFormConfiguration
{

    public abstract static function identifier(): string;
    public abstract static function displayName(): string;

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
    public static function displayMode():string {
        return 'default';
    }

    public static function getOverwriteViewModes():array {
        return [];
    }

}
