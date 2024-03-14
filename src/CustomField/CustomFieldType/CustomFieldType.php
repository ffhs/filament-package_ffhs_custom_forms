<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;

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

    public static function getGeneralFieldTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.general_field_types") as $typeClass)
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

    public function getViewModes(?string $dynamicFormConfiguration = null):array {
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

    public function getExtraTypeOptionComponent(): ?Component{
        if(!$this->hasExtraTypeOptions()) return null;
        $components = [];
        foreach ($this->getExtraTypeOptions() as $key => $option ){
            /**@var TypeOption $option*/
            $component =  $option->getComponent()->statePath("options.".$key);
            $components[] = $component;
        }
        return Section::make()
            ->schema($components)
            ->statePath("options")
            ->columns();
    }

    public function getExtraGeneralTypeOptionComponents(): array{
        if(!$this->hasExtraGeneralTypeOptions()) return [];
        $components = [];
        foreach ($this->getExtraGeneralTypeOptions() as $key => $option ){
            /**@var TypeOption $option*/
            $component =  $option->getComponent()->id($key);
            $components[] = $component;
        }
        return $components;
    }

}
