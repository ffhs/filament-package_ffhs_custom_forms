<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Database\Eloquent\Collection;

abstract class CustomFormConfiguration
{
    abstract public static function displayName(): string;

    final public static function make(): static
    {
        return app(static::class);
    }

    public static function formFieldTypes(): array
    {
        return CustomFieldType::getSelectableFieldTypes();
    }


    public static function ruleTypes(): array
    {
        return config("ffhs_custom_forms.field_rule_types");
    }


    public static function anchorRuleTypes(): array
    {
        return config("ffhs_custom_forms.field_rule_anchor_types");
    }

    public static function editorFieldAdder(): array
    {
        return config("ffhs_custom_forms.editor.field_adders");
    }


    public static function displayViewMode(): string
    {
        return self::defaultViewMode();
    }

    public static function defaultViewMode(): string
    {
        return 'default';
    }

    public static function displayEditMode(): string
    {
        return self::defaultViewMode();
    }

    public static function displayCreateMode(): string
    {
        return self::defaultViewMode();
    }

    public static function overwriteViewModes(): array
    {
        return [];
    }

//    public static function editorValidations(CustomForm $form): array
//    {
//        //If it's a template, it hasn't to be checked
//        if ($form->is_template) {
//            return [];
//        }
//        return config("ffhs_custom_forms.custom_form_editor_validations");
//    } ToDo may reimplement?

    abstract public static function identifier(): string;

    public function getAvailableTemplates(): Collection
    {
        return once(function () {
            return CustomForm::query()
                ->with('customFields')
                ->whereNotNull('template_identifier')
                ->where('custom_form_identifier', $this::identifier())
                ->get()
                ->keyBy('id');
        });
    }

    public function getAvailableGeneralFields(): Collection
    {
        return once(function () {
            $generalFieldFormQuery = GeneralFieldForm::query()
                ->select('general_field_id')
                ->where('custom_form_identifier', $this::identifier());

            return GeneralField::query()
                ->with('generalFieldForms')
                ->whereIn('id', $generalFieldFormQuery)
                ->get();
        });
    }
}
