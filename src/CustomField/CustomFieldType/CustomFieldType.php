<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\EditAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\NewEggActionComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullInLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullInNestedLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullOutLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullOutNestedLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

abstract class CustomFieldType
{

    /*
     * Static Class Functions
     */
    public static function getAllTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.custom_field_types") as $typeClass)
            $output[$typeClass::getFieldIdentifier()]= $typeClass;
        return $output;
    }

    public static function getSelectableGeneralFieldTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.selectable_general_field_types") as $typeClass)
            $output[$typeClass::getFieldIdentifier()]= $typeClass;
        return $output;
    }
    public static function getSelectableFieldTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.selectable_field_types") as $typeClass)
            $output[$typeClass::getFieldIdentifier()]= $typeClass;
        return $output;
    }


    public static function getTypeClassFromName(string $typeName): ?string {
        $types = self::getAllTypes();
        if(!array_key_exists($typeName,$types)) return null;
        return self::getAllTypes()[$typeName];
    }

    public static function getTypeFromName(string $typeName): ?CustomFieldType {
        $class = self::getTypeClassFromName($typeName);
        if(is_null($class)) return null;
        return new $class();
    }


    public function getFormComponent(CustomField $record, CustomForm $form, string $viewMode = "default", array $parameter = []): Component { //ToDo Remove Parameters?
        $viewMods = $this->getViewModes($form->getFormConfiguration());
        //FieldTypeView.php
        if(empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getFormComponent($this,$record,$parameter);
    }
    public function getInfolistComponent(CustomFieldAnswer $record,CustomForm $form, string $viewMode = "default", array $parameter = []): \Filament\Infolists\Components\Component{
        $viewMods = $this->getViewModes($form->getFormConfiguration());
        //FieldTypeView.php
        if(empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getInfolistComponent($this,$record,$parameter);
    }

    public function getViewModes(null|string|DynamicFormConfiguration $dynamicFormConfiguration = null):array {
        $viewMods = $this->viewModes();

        /**@var DynamicFormConfiguration $dynamicFormConfig*/
        $dynamicFormConfig = new $dynamicFormConfiguration();

        //Config Overwrite
        $overWrittenLevelOne = $this->overwriteViewModes();
        if(!empty($overWrittenLevelOne))  foreach($overWrittenLevelOne as $key => $value) $viewMods[$key] = $value;

        if(is_null($dynamicFormConfiguration)) return $this->viewModes();

        // Form Overwritten
        $overWrittenLevelTwo = $dynamicFormConfig::overwriteViewModes();
        if(!empty($overWrittenLevelTwo) && !empty($overWrittenLevelTwo[$this::class])){
            foreach($overWrittenLevelTwo[$this::class] as $key => $value)$viewMods[$key] = $value;
        }

        return $viewMods;
    }

    public function overwriteViewModes():array{
        $viewModes = config("ffhs_custom_forms.view_modes");
        if(empty($viewModes[$this::class])) return [];
        return $viewModes[$this::class];
    }



    // Extra Options
    public function hasExtraTypeOptions():bool{
        return !empty($this->getExtraTypeOptions());
    }
    public function hasExtraGeneralTypeOptions():bool{
        return !empty($this->getExtraGeneralTypeOptions());
    }

    public function getExtraTypeOptionComponents(): array{
        if(!$this->hasExtraTypeOptions()) return [];
        $components = [];
        foreach ($this->getExtraTypeOptions() as $key => $option ){
            /**@var TypeOption $option*/
            $component =  $option->getModifyComponent($key);
            $components[] = $component;
        }
        return $components;
    }

    public function getExtraGeneralTypeOptionComponents(): array{
        if(!$this->hasExtraGeneralTypeOptions()) return [];
        $components = [];
        foreach ($this->getExtraGeneralTypeOptions() as $key => $option ){
            /**@var TypeOption $option*/
            $component =  $option->getModifyComponent($key);
            $components[] = $component;
        }
        return $components;
    }

    public function getDefaultTypeOptionValues():array {
        $defaults = [];
        foreach ($this->getExtraTypeOptions() as $key => $extraTypeOption){
            /**@var TypeOption $extraTypeOption*/
            $defaults[$key] = $extraTypeOption->getModifyDefault();
        }
        return $defaults;
    }

    public function getDefaultGeneralOptionValues():array {
        $defaults = [];
        foreach ($this->getExtraGeneralTypeOptions() as $key => $extraTypeOption){
            /**@var TypeOption $extraTypeOption*/
            $defaults[$key] = $extraTypeOption->getModifyDefault();
        }
        return $defaults;
    }




    /*
     * User Stuff
     */

    public static abstract function getFieldIdentifier():String;
    public abstract function viewModes():array;
    public abstract function icon():String;



    public function prepareSaveFieldData(mixed $data): ?array{
        if(is_null($data)) return null;
        return ["saved"=> $data];
    }
    public function prepareLoadFieldData(array $data): mixed{
        if(!array_key_exists("saved",$data) || is_null($data["saved"])) return null;
        return $data["saved"];
    }

    public function getTranslatedName():String{
        return __("custom_forms.types." . $this::getFieldIdentifier());
    }

    public function getExtraTypeOptions():array{
        return [];
    }
    public function getExtraGeneralTypeOptions():array{
        return [];
    }

    //ToDo Mutate answerers (Save,  Create)
    public function canBeDeactivate():bool {
        return true;
    }
    public function canBeRequired():bool {
        return true;
    }
    public function hasToolTips():bool {
        return true;
    }


    // null means that it isn't overwritten
    public function overwrittenRules():?array { //ToDo
        return null;
    }

    // null means that it isn't overwritten
    public function overwrittenAnchorRules():?array { //ToDo
        return null;
    }

    public function afterEditFieldSave(CustomField $field, array $rawData):void {

    }
    public function afterEditFieldDelete(CustomField $field):void {
    }


    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData):void {

    }

    public function mutateOnTemplateDissolve(array $data, CustomField $original):array {
        return $data;
    }


    public function nameFormEditor(array $state): string|null {
        if(empty($state["general_field_id"])) return $state["name_de"];//ToDo Translate;
        $genField = GeneralField::cached($state["general_field_id"]);
        return $genField->name_de; //ToDo Translate;
    }

    public function nameBeforeIconFormEditor(array $state):string|null {
        $badges = "";
        if(!empty($state["general_field_id"])) $badges .= new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)"));
        if(!$state["is_active"]) $badges .= new HtmlBadge("Deaktiviert", Color::rgb("rgb(194, 53, 35)")); //ToDo translate
        return $badges;
    }


    public function repeaterFunctions():array{
        return [
            PullInLayoutAction::class => PullInLayoutAction::getDefaultTypeClosure($this),
            PullOutLayoutAction::class=> PullOutLayoutAction::getDefaultTypeClosure($this),

            //Nested Layout Functions
            PullInNestedLayoutAction::class => PullInNestedLayoutAction::getDefaultTypeClosure($this),
            PullOutNestedLayoutAction::class => PullOutNestedLayoutAction::getDefaultTypeClosure($this),

            NewEggActionComponent::class => NewEggActionComponent::getDefaultTypeClosure(null), //<- Only for the position

            EditAction::class => RepeaterFieldAction::getDefaultTypeClosure($this),
        ];
    }

    //Empty or null mean that the repeater cant open
    public function editorRepeaterContent(CustomForm $form, array $fieldData):?array {
        return null;
    }


    public function getEditorItemTitle(array $state, CustomForm $form):mixed {
        //Before Icon
        $html = $this->nameBeforeIconFormEditor($state);

        //Prepare the Icon
        $icon = Blade::render('<x-'. $this->icon() .' class="h-4 w-4"/>');
        $icon = '<span class="px-2 py-1"> ' .$icon . '</span>';
        $html.= $icon;

        //Name
        $nameStyle = 'class="text-sm font-medium ext-gray-950 dark:text-white truncate select-none"';
        $name = $this->nameFormEditor($state);
        $html.= '<h4'.$nameStyle.'>' . $name . '</h4>';

        //Do Open the Record if possible
        $clickAction = '';
        if(!empty($this->editorRepeaterContent($form,$state)))
            $clickAction= 'x-on:click.stop="isCollapsed = !isCollapsed"';


        $html= '<span  class="cursor-pointer flex"'.$clickAction.'>' . $html . '</span>';

        //Close existing heading and after that reopen it
        $html=  '</h4>'. $html .'<h4>';
        return  new HtmlString($html);
    }

    public function getConfigAttribute(string $attribute):mixed {
        return config("ffhs_custom_forms.type_settings." . $this::getFieldIdentifier() . "." . $attribute);
    }

}
