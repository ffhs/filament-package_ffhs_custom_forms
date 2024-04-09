<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;

abstract class CustomFieldType extends CustomFieldTypeMethods
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
    public static function getSelecteableFieldTypes():array{
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
            $defaults[$key] = $extraTypeOption->getDefaultValue();
        }
        return $defaults;
    }

    public function getDefaultGeneralOptionValues():array {
        $defaults = [];
        foreach ($this->getExtraGeneralTypeOptions() as $key => $extraTypeOption){
            /**@var TypeOption $extraTypeOption*/
            $defaults[$key] = $extraTypeOption->getDefaultValue();
        }
        return $defaults;
    }



}
