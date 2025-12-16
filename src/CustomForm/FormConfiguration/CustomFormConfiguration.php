<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

abstract class CustomFormConfiguration
{
    protected static string $identifier;
    protected static string $displayname;


    final public static function make(): static
    {
        return app(static::class);
    }

    public static function config(string $config, $default = null)
    {
        return CustomForms::config('form_configurations.' . static::class . '.' . $config, $default) ??
            CustomForms::config('default_form_configuration.' . $config, $default);
    }

    /**
     * @return array<class-string<CustomFieldType>, array<string, class-string<FieldTypeView>>>
     */
    public static function overwriteViewModes(): array
    {
        return [];
    }

    public static function identifier(): string
    {
        if (!isset(static::$identifier)) {
            throw new RuntimeException(static::class . ' has no $identifier set');
        }

        return static::$identifier;
    }

    public static function displayname(): string
    {
        if (!isset(static::$displayname)) {
            throw new RuntimeException(static::class . ' has no $identifier set');
        }

        return static::$displayname;
    }

    public function getRuleTriggerTypes(): array
    {
        return $this::config('rule.trigger') ?? [];
    }

    public function getRuleEventTypes(): array
    {
        return $this::config('rule.event') ?? [];
    }

    public function getSelectableFieldTypeClasses(): array
    {
        return static::config('selectable_field_types');
    }

    public function getSelectableFieldTypes(): array
    {
        return once(function () {
            $classes = $this->getSelectableFieldTypeClasses();
            $types = [];
            foreach ($classes as $class) {
                /**@var CustomFieldType $class */
                $types[$class::identifier()] = $class::make();
            }
            return $types;
        });

    }

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

    /**
     * @return Collection<int|string, CustomForm>
     */
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

    /**
     * @return Collection<int|string, GeneralField>
     */
    final public function getAvailableGeneralFields(): Collection
    {
        return once(function () {
            $generalFieldFormQuery = GeneralFieldForm::query()
                ->select('general_field_id')
                ->where('custom_form_identifier', $this::identifier());

            return GeneralField::query()
                ->with('generalFieldForms')
                ->whereIn('id', $generalFieldFormQuery)
                ->get()
                ->keyBy('id');
        });
    }

    public function getColumns()
    {
        return $this::config('column_count', 4);
    }

    public function getSideComponentModifiers()
    {
        return $this::config('editor.side_components') ?? [];
    }
}
