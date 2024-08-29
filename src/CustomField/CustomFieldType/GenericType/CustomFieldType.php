<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasConfigAttribute;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasEditFieldCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultCustomFieldEditTypeOptionsAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\html\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
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

    /*
     * Static used functions
     */
    public final static function getConfigTypeList(): string {
        return "custom_field_types";
    }

    public final static function getSelectableGeneralFieldTypes(): array {
        $output = [];
        foreach (config("ffhs_custom_forms.selectable_general_field_types") as $typeClass)
            $output[$typeClass::identifier()] = $typeClass;
        return $output;
    }

    public final static function getSelectableFieldTypes(): array {
        $output = [];
        foreach (config("ffhs_custom_forms.selectable_field_types") as $typeClass)
            $output[$typeClass::identifier()] = $typeClass;
        return $output;
    }


    /*
     * Fields
     */

    //public static abstract function identifier(): string;
    public abstract function viewModes(): array;
    public abstract function icon(): string;


    public function prepareSaveFieldData(mixed $data): ?array { //ToDo Rename and in Template
        if (is_null($data)) return null;
        return ["saved" => $data];
    }
    public function prepareLoadFieldData(array $data): mixed { //ToDo Rename and in Template
        if (!array_key_exists("saved", $data) || is_null($data["saved"])) return null;
        return $data["saved"];
    }



    public function getTranslatedName(): string {
        return __("custom_forms.types." . $this::identifier());
    }




    // null means that it isn't overwritten
    public function overwrittenRules(): ?array { //ToDo implement
        return null;
    }

    // null means that it isn't overwritten
    public function overwrittenAnchorRules(): ?array { //ToDo implement
        return null;
    }
    public function hasRules(): ?array { //ToDo implement
        return null;
    }





    public function canBeDeactivate(): bool {
          return true;
    }

    public function fieldEditorExtraComponent(array $fieldData): ?string {
        return null;
    }


    public function getEditorFieldTitle(array $fieldData):string {
        $field = new CustomField();
        $field->fill($fieldData);

        if(!$field->isGeneralField()) return $this->getTranslatedName();

        return "<div>". new HtmlBadge('Gen', Color::rgb('rgb(43, 164, 204)'))."</div>" .
            '<p style="margin-left: 40px; margin-top: -20px">'. $field->name.'</p>'; //ToDo Badges function reimplement
    }
    public function getEditorFieldIcon(array $fieldData):string
    {
        $field = new CustomField();
        $field->fill($fieldData);

        if(!$field->isGeneralField()) return $this->icon();
        else return $field->generalField->icon;
    }

    public function getEditorActions(string $key, array $fieldState): array{
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            // DefaultCustomRulesAction::make('edit-rule-field-' . $key), //toDo Delete Class
            DefaultCustomFieldEditTypeOptionsAction::make('edit-field-' . $key),
            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }


    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData): void { //ToDo to Traits
    }


    //You can interact with the Component like in FileUpload
    public function updateFormComponentOnSave(Component $component, CustomField $customField, Form $form, Collection $flattenFormComponents): void {//ToDo to Traits
    }

    public function hasEditorNameElement(array $fielData):bool {
        return empty($fielData['general_field_id']);
    }

    public function mutateOnTemplateDissolve(array $data, CustomField $original): array {
        return $data; //ToDo Reimplement
    }

    public function isFullSizeField(): bool
    {
        return false;
    }

    public function getStaticColumns() :int|null
    {
        return null;
    }


}
