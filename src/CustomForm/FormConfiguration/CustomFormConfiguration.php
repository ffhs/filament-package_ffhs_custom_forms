<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
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
        return config('ffhs_custom_forms.field_rule_types');
    }


    public static function anchorRuleTypes(): array
    {
        return config('ffhs_custom_forms.field_rule_anchor_types');
    }

    public static function editorFieldAdder(): array
    {
        return config('ffhs_custom_forms.editor.field_adders');
    }

    public static function overwriteViewModes(): array
    {
        return [];
    }

    abstract public static function identifier(): string;

    public function displayViewMode(): string
    {
        return $this->defaultViewMode();
    }

    public function defaultViewMode(): string
    {
        return 'default';
    }

    public function displayEditMode(): string
    {
        return $this->defaultViewMode();
    }

//    public static function editorValidations(CustomForm $form): array
//    {
//        //If it's a template, it hasn't to be checked
//        if ($form->is_template) {
//            return [];
//        }
//        return config('ffhs_custom_forms.custom_form_editor_validations');
//    } ToDo may reimplement?

    public function displayCreateMode(): string
    {
        return $this->defaultViewMode();
    }

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
