<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Support\Facades\App;

abstract class CustomFieldType
{

    /*
     * Static Class Functions
     */
    public static function getAllTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.custom_field_types") as $typeClass){
            $output[$typeClass::getFieldIdentifier()]= $typeClass;
        }
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

    public static function prepareCloneOptions(array $variationData, string $target, $set, Get $get) :array{
        return $variationData["options"];
    }



    public static abstract function getFieldIdentifier():String;
    public abstract function viewModes():array;
    public abstract function icon():String;



    public function getFormComponent(CustomFieldVariation $record, CustomForm $form, string $viewMode = "default", array $parameter = []): Component { //ToDo Remove Parameters?
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

        //Config Overwrite
        $overWrittenLevelOne = $this->overwriteViewModes();
        if(!empty($overWrittenLevelOne)){
            foreach($overWrittenLevelOne as $key => $value) $viewMods[$key] = $value;
        }

        if(is_null($dynamicFormConfiguration)) return $this->viewModes();

        // Form Overwritten
        $overWrittenLevelTwo = $dynamicFormConfiguration::overwriteViewModes();
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



    public static function getToolTips(CustomFieldVariation|CustomFieldAnswer $record) :?string{
        if($record instanceof  CustomFieldAnswer) $record = $record->customFieldVariation;
        return  $record->customField->getInheritState()["tool_tip_" . App::currentLocale()];
    }
    public static function getIdentifyKey(CustomFieldVariation|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customFieldVariation;
        return  $record->customField->getInheritState()["identify_key"];
    }
    public static function getLabelName(CustomFieldVariation|CustomFieldAnswer  $record) :String{
        if($record instanceof  CustomFieldAnswer) $record = $record->customFieldVariation;
        return  $record->customField->getInheritState()["name_" . App::currentLocale()];
    }


    public function answare(CustomFieldAnswer $answer) {
        $rawAnswerer = $answer->answer;
        if(is_null($rawAnswerer)) return null;
        return $this->prepareLoadFieldData($rawAnswerer);
    }


    public function prepareSaveFieldData(mixed $data): ?array{
        if(is_null($data)) return null;
        return ["saved"=> $data];
    }
    public function prepareLoadFieldData(array $data): mixed{
        if(empty($data["saved"])) return null;
        return $data["saved"];
    }




    public function getTranslatedName():String{
        return __("custom_forms.types." . self::fieldIdentifier());
    }


    public function fieldIdentifier():String{return $this::getFieldIdentifier();}



    // Extra Options
    public function getExtraOptionSchema():?array{
        return null;
    }

    public function getExtraOptionFields():array{
        return [];
    }

    public function getGeneralExtraField(): ?array{
        return  null;
    }


    public function hasExtraOptions():bool{
        return !empty($this->getExtraOptionFields());
    }

    public function getExtraOptionsComponent(): ?Component{
        if(!$this->hasExtraOptions()) return null;
        return Section::make()
            ->schema($this->getExtraOptionSchema())
            ->statePath("options")
            ->columns();
    }

    public function mutateVariationDataBeforeFill(array $data):array{
         if(!array_key_exists("options",$data) || is_null($data["options"])) $data["options"] = $this->getExtraOptionFields();
         return $data;
    }

    public function mutateVariationDataBeforeSave(array $data):array{
        if(!array_key_exists("options",$data)  || empty($data["options"]))  $data["options"] = null;
        if(!$this->canBeDeactivate()) $data["is_active"] = true;
        if(!$this->canBeRequired()) $data["required"] = false;
        return $data;
    }


    public function mutateVariationDataBeforeCreate(array $data):array{
        return $this->mutateVariationDataBeforeSave($data);
    }

    public function mutateCustomFieldDataBeforeFill(array $data):array{
        return $data;
    }

    public function mutateCustomFieldDataBeforeSave(?array $data):array{
        return $data;
    }

    public function mutateCustomFieldDataBeforeCreate(array $data):array{
        if(empty($data["identify_key"]) && empty($data["general_field_id"])) $data["identify_key"] = uniqid();
        return $this->mutateCustomFieldDataBeforeSave($data);
    }

    public function afterCustomFieldSave(CustomField $field, array$data):void{
    }
    public function afterCustomFieldVariationSave(?CustomFieldVariation $variation, array $variationData):void {
    }

    //ToDo Mutate answerers (Save,  Create)


    public function canBeDeactivate():bool {
        return true;
    }
    public function canHasVariations():bool {
        return true;
    }
    public function canBeRequired():bool {
        return true;
    }

    public function getOptionParameter(CustomFieldVariation|CustomFieldAnswer $record, string $option){
        if($record instanceof CustomFieldAnswer) $record=$record->customFieldVariation;
        if(array_key_exists($option, $record->options)) return $record->options[$option];
        return $this->getExtraOptionFields()[$option];
    }




}
