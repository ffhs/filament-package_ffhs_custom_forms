<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;



use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\App;
use function PHPUnit\Framework\isEmpty;

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

    /*
     * Abstract and Instance Functions
     */



    public static abstract function getFieldIdentifier():string;
    public abstract function viewModes():array;
    public abstract function  icon():string;


    public function getFormComponent(CustomFieldVariation $record, string $viewMode = "default", array $parameter = []): Component { //ToDo Remove Parameters?
        $viewMods = $this->getViewModes($record->customField->customForm->getFormConfiguration());
        //FieldTypeView.php
        if(empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getFormComponent($this,$record,$parameter);
    }
    public function getInfolistComponent(CustomFieldAnswer $record,string $viewMode = "default", array $parameter = []): \Filament\Infolists\Components\Component{
        $viewMods = $this->getViewModes($record->customFieldVariation->customField->customForm->getFormConfiguration());
        //FieldTypeView.php
        if(empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getInfolistComponent($this,$record,$parameter);
    }


    public function getViewModes(?string $dynamicFormConfiguration = null):array {
        $viewMods = $this->viewModes();

        //Config Overwrite
        $overWrittenLevelOne = $this->overwriteViewModes();
        if(!empty($overWrittenLevelOne) && !empty($overWrittenLevelOne[self::class])){
            foreach($overWrittenLevelOne[$this::class] as $key => $value) $viewMods[$key] = $value;
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
    public static function getIdentifyKey(CustomFieldVariation|CustomFieldAnswer  $record) :string{
        if($record instanceof  CustomFieldAnswer) $record = $record->customFieldVariation;
        return  $record->customField->getInheritState()["identify_key"];
    }
    public static function getLabelName(CustomFieldVariation|CustomFieldAnswer  $record) :string{
        if($record instanceof  CustomFieldAnswer) $record = $record->customFieldVariation;
        return  $record->customField->getInheritState()["name_" . App::currentLocale()];
    }


    public static function prepareCloneOptions(array $templateOptions, bool $isInheritGeneral) :array{
        return $templateOptions;
    }


    public function prepareSaveFieldData(mixed $data): ?array{
        if(is_null($data)) return null;
        return ["saved"=> $data];
    }
    public function prepareLoadFieldData(array $data): mixed{
        if(empty($data["saved"])) return null;
        return $data["saved"];
    }


    public function getTranslatedName():string{
        return __("custom_forms.types." . self::fieldIdentifier());
    }


    public function fieldIdentifier():string{return $this::getFieldIdentifier();}


    // Extra Options
    public function getExtraOptionSchema():?array{
        return null;
    }

    public function getExtraOptionFields():array{
        return [];
    }

    public function getGeneralFieldExtraField(): ?Component{
        return  null;
    }

    public function hasExtraOptions():bool{
        return !empty($this->getExtraOptionFields());
    }

    public function getExtraOptionsRepeater(): ?Repeater{
        if(!$this->hasExtraOptions()) return null;
        return Repeater::make("options")
            ->schema($this->getExtraOptionSchema())
            ->reorderable(false)
            ->deletable(false)
            ->cloneable(false)
            ->addable(false)
            ->defaultItems(1)
            ->columns(2)
            ->maxItems(1)
            ->minItems(1)
            ->label("")
            ->live();
    }

    public function prepareOptionDataBeforeFill(array $data):array{
         if(!array_key_exists("options",$data) || is_null($data["options"])) $data["options"] = [0=> $this->getExtraOptionFields()];
         else if(!array_key_exists(0,$data["options"]))$data["options"] = [0 => $data["options"]];
         return $data;
    }

    public function prepareOptionDataBeforeSave(?array $data):array{
        if(array_key_exists("options",$data) && !empty($data["options"]))
            $data["options"] = $data["options"][0];
        else
            $data["options"] = null;
        return $data;
    }

    public function prepareOptionDataBeforeCreate(array $data):array{
        $data["identify_key"]= uniqid();
        return $this->prepareOptionDataBeforeSave($data);
    }


    public function getOptionParameter(CustomFieldVariation|CustomFieldAnswer $record, string $option){
        if($record instanceof CustomFieldAnswer) $record=$record->customFieldVariation;
        if(array_key_exists($option, $record->options)) return $record->options[$option];
        return $this->getExtraOptionFields()[$option];
    }


}
