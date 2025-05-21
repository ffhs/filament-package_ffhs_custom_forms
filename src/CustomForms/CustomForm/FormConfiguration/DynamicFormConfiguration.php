<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

abstract class DynamicFormConfiguration
{

    public abstract static function displayName(): string;

    public  static function formFieldTypes():array{
        return CustomFieldType::getSelectableFieldTypes();
    }

    //Types

    public static function ruleTypes(): array{
        return config("ffhs_custom_forms.field_rule_types");
    }

    //Rules

    public static function anchorRuleTypes(): array {
        return config("ffhs_custom_forms.field_rule_anchor_types");
    }

    public static function editorFieldAdder():array {
        return config("ffhs_custom_forms.editor.field_adders");
    }

    //Editor Adder's

    public static function displayViewMode():String {
        return self::displayMode();
    }

    // ViewModes

    public static function displayMode():String {
        return 'default';
    }

    public static function displayEditMode():String {
        return self::displayMode();
    }
    public static function displayCreateMode():String {
        return self::displayMode();
    }

    public static function overwriteViewModes():array {
        return [];
    }

    public static function editorValidations(CustomForm $form):array {
        //If it's a template, it hasn't to be checked
        if($form->is_template) return [];
        return config("ffhs_custom_forms.custom_form_editor_validations");
    }

    //Editor Validation

    public final static function getFormConfigurationClass(string $custom_form_identifier):String {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $custom_form_identifier)->first();
    }

    // All

    public abstract static function identifier(): string;

}
