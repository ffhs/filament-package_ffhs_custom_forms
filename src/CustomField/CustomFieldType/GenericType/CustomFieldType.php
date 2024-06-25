<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasConfigAttribute;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasEditFieldCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions\DefaultCustomFieldEditAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasTypeView;

abstract class CustomFieldType extends Type
{
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



    public static abstract function identifier(): string;
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
    public function overwrittenRules(): ?array { //ToDo
        return null;
    }

    // null means that it isn't overwritten
    public function overwrittenAnchorRules(): ?array { //ToDo
        return null;
    }
    public function hasRules(): ?array { //ToDo
        return null;
    }




    //ToDo Mutate answerers (Save,  Create)
    public function canBeDeactivate(): bool {
          return true;
         }



    public function fieldEditorExtraComponent(array $fieldData): ?string {
        return null;
    }


    public function getEditorActions(string $key, array $fieldState): array{
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            DefaultCustomFieldEditAction::make('edit-field-' . $key),
            DefaultCustomActivationAction::make('active-' . $key),
        ];
    }







    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData): void { //ToDo to Traits
    }


    //You can interact with the Component like in FileUpload
    public function updateFormComponentOnSave(Component $component, CustomField $customField, Form $form, Collection $flattenFormComponents): void {//ToDo to Traits
    }



    public function mutateOnTemplateDissolve(array $data, CustomField $original): array {
        return $data; //ToDo Reimplement
    }

    /*  public function nameFormEditor(array $state): string|null {
         $field = new CustomField();
         $field->fill($state);
         return $field->name;
     }

     public function nameBeforeIconFormEditor(array $state): string|null {
         $badges = "";
         if (!empty($state["general_field_id"])) $badges .= new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)"));
         if (!$state["is_active"]) $badges .= new HtmlBadge("Deaktiviert", Color::rgb("rgb(194, 53, 35)")); //ToDo translate
         return $badges;
     }

     /*public function repeaterFunctions(): array {
         return [
             PullInLayoutAction::class => PullInLayoutAction::getDefaultTypeClosure($this),
             PullOutLayoutAction::class => PullOutLayoutAction::getDefaultTypeClosure($this),

             //Nested Layout Functions
             PullInNestedLayoutAction::class => PullInNestedLayoutAction::getDefaultTypeClosure($this),
             PullOutNestedLayoutAction::class => PullOutNestedLayoutAction::getDefaultTypeClosure($this),

             NewEggActionComponent::class => NewEggActionComponent::getDefaultTypeClosure(null), //<- Only for the position

             EditAction::class => RepeaterFieldAction::getDefaultTypeClosure($this),
         ];
     }

     //Empty or null mean that the repeater cant open
     public function editorRepeaterContent(CustomForm $form, array $fieldData): ?array {
         return null;
     }

    public function getEditorItemTitle(array $state, CustomForm $form): mixed {
         //Before Icon
         $html = $this->nameBeforeIconFormEditor($state);

         //Prepare the Icon
         $icon = Blade::render('<x-' . $this->icon() . ' class="h-4 w-4"/>');
         $icon = '<span class="px-2 py-1"> ' . $icon . '</span>';
         $html .= $icon;

         //Name
         $nameStyle = 'class="text-sm font-medium ext-gray-950 dark:text-white truncate select-none"';
         $name = $this->nameFormEditor($state);
         $html .= '<h4' . $nameStyle . '>' . $name . '</h4>';

         //Do Open the Record if possible
         $clickAction = '';
         if (!empty($this->editorRepeaterContent($form, $state)))
             $clickAction = 'x-on:click.stop="isCollapsed = !isCollapsed"';

         $html = '<span  class="cursor-pointer flex"' . $clickAction . '>' . $html . '</span>';

         //Close existing heading and after that reopen it
         $html = '</h4>' . $html . '<h4>';
         return new HtmlString($html);
     }*/


}
