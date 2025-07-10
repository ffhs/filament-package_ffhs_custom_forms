<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
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

    public static function getRuleTriggerTypes(): array
    {
        return config('ffhs_custom_forms.rule.trigger');
    }

    public static function getRuleEventTypes(): array
    {
        return config('ffhs_custom_forms.rule.event');
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

    public function displayCreateMode(): string
    {
        return $this->defaultViewMode();
    }

    final public function getAvailableTemplates(): Collection
    {
        return once(function () {
            $forms = CustomForm::query()
                ->with('ownedFields', 'ownedGeneralFields')
                ->whereNotNull('template_identifier')
                ->where('custom_form_identifier', $this::identifier())
                ->get()
                ->keyBy('id');
            $forms->each(fn(CustomForm $form) => $form->setRelation('customFields', $form->ownedFields));
            $forms->each(fn(CustomForm $form) => $form->setRelation('generalFields', $form->ownedGeneralFields));

            CustomForms::cacheForm($forms);

            return $forms;
        });
    }

    final public function getAvailableGeneralFields(): Collection
    {
        return once(function () {
            $generalFieldFormQuery = GeneralFieldForm::query()
                ->with('customOptions')
                ->select('general_field_id')
                ->where('custom_form_identifier', $this::identifier());

            return GeneralField::query()
                ->with('generalFieldForms')
                ->whereIn('id', $generalFieldFormQuery)
                ->get()
                ->keyBy('id');
        });
    }
}
