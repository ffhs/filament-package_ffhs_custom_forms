<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;

abstract class DynamicFormConfiguration
{

    public abstract static function identifier(): string;
    public abstract static function displayName(): string;

    public  static function formFieldTypes():array{
        $types = CustomFieldType::getAllTypes();
        unset($types[TemplateFieldType::getFieldIdentifier()]);
        return $types;
    }

    public static function ruleTypes(): array{
        return config("ffhs_custom_forms.field_rule_types");
    }
    public static function anchorRuleTypes(): array {
        return config("ffhs_custom_forms.field_rule_anchor_types");
    }

    public static function displayViewMode():String {
        return self::displayMode();
    }
    public static function displayEditMode():String {
        return self::displayMode();
    }
    public static function displayCreateMode():String {
        return self::displayMode();
    }


    public static function displayMode():String {
        return 'default';
    }


    public static function overwriteViewModes():array {
        return [];
    }


    public final static function getFormConfigurationClass(string $custom_form_identifier):String {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $custom_form_identifier)->first();
    }

}
