<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

abstract class DynamicFormConfiguration
{

    public abstract static function identifier(): string;
    public abstract static function displayName(): string;

    //Types
    public  static function formFieldTypes():array{
        return CustomFieldType::getSelectableFieldTypes();
    }

    //Rules
    public static function ruleTypes(): array{
        return config("ffhs_custom_forms.field_rule_types");
    }
    public static function anchorRuleTypes(): array {
        return config("ffhs_custom_forms.field_rule_anchor_types");
    }

    //Editor Adder's
    public static function editorFieldAdder():array {
        return config("ffhs_custom_forms.editor.field_adders");
    }

    // ViewModes
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

    //Editor Validation
    public static function editorValidations(CustomForm $form):array {
        //If it's a template, it hasn't to be checked
        if($form->is_template) return [];
        return config("ffhs_custom_forms.custom_form_editor_validations");
    }

    // All
    public final static function getFormConfigurationClass(string $custom_form_identifier):String {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $custom_form_identifier)->first();
    }

}
