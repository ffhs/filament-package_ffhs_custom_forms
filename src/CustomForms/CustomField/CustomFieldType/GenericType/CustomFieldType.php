<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\Type;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasConfigAttribute;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasEditFieldCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasFieldSplitting;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasGridModifiers;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomFieldEditTypeOptionsAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\html\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsType;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

abstract class CustomFieldType implements Type
{
    use IsType;
    use HasTypeView;
    use HasTypeOptions;
    use HasConfigAttribute;
    use HasEditFieldCallbacks;
    use HasGridModifiers;
    use HasFieldSplitting;

    final public static function getConfigTypeList(): string
    {
        return "custom_field_types";
    }


    final public static function getSelectableGeneralFieldTypes(): array
    {
        $output = [];
        foreach (config("ffhs_custom_forms.selectable_general_field_types") as $typeClass) {
            $output[$typeClass::identifier()] = $typeClass;
        }
        return $output;
    }

    final public static function getSelectableFieldTypes(): array
    {
        $output = [];
        foreach (config("ffhs_custom_forms.selectable_field_types") as $typeClass) {
            $output[$typeClass::identifier()] = $typeClass;
        }
        return $output;
    }

    public static function make(): static
    {
        return app(static::class);
    }


    /*
     * Fields
     */

    //public static abstract function identifier(): string;

    public abstract function viewModes(): array;

    public function prepareSaveFieldData(CustomFieldAnswer $answer, mixed $data): ?array
    { //ToDo Rename and in Template
        if (is_null($data)) {
            return null;
        }
        return ["saved" => $data];
    }

    public function prepareLoadFieldData(CustomFieldAnswer $answer, array $data): mixed
    { //ToDo Rename and in Template
        if (!array_key_exists("saved", $data) || is_null($data["saved"])) {
            return null;
        }
        return $data["saved"];
    }

    public function fieldEditorExtraComponent(array $fieldData): ?string
    {
        return null;
    }

    public function getEditorFieldTitle(array $fieldData): string
    {
        $field = new CustomField();
        $field->fill($fieldData);

        if (!$field->isGeneralField()) {
            return $this->getTranslatedName();
        }

        return "<div>" . new HtmlBadge('Gen', Color::rgb('rgb(43, 164, 204)')) . "</div>" .
            '<p style="margin-left: 40px; margin-top: -20px">' . $field->name . '</p>'; //ToDo Badges function reimplement
    }

    public function getTranslatedName(): string
    {
        return __('custom_forms.types.' . $this::identifier());
    }

    public function getEditorFieldIcon(array $fieldData): string
    {
        $field = new CustomField();
        $field->fill($fieldData);

        if (!$field->isGeneralField()) {
            return $this->icon();
        } else {
            return $field->generalField->icon;
        }
    }

    abstract public function icon(): string;

    public function getEditorActions(string $key, array $fieldState): array
    {
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            DefaultCustomFieldEditTypeOptionsAction::make('edit-field-' . $key),
            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }

    public function canBeDeactivate(): bool
    {
        return true;
    }

    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData): void
    { //ToDo to Traits
    }


    //You can interact with the Component like in FileUpload
    public function updateFormComponentOnSave(
        Component $component,
        CustomField $customField,
        Form $form,
        Collection $flattenFormComponents
    ): void {//ToDo to Traits
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return empty($fielData['general_field_id']);
    }

    public function mutateOnCloneField(array $data, CustomField $original): array
    {
        return $data;
    }

    public function isEmptyAnswerer(CustomFieldAnswer $customFieldAnswer, ?array $fieldAnswererData): bool
    {
        if (empty($fieldAnswererData) && !is_bool($fieldAnswererData)) {
            return true;
        }
        if (empty($fieldAnswererData["saved"] ?? []) && sizeof($fieldAnswererData) == 1 && !is_bool($fieldAnswererData["saved"])) {
            return true;
        }
        return false;
    }


}
