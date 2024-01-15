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

    public function getFormComponent(CustomFieldVariation $record, string $viewMode = "default", array $parameter = []): Component { //ToDo Remove Parameters?
        $viewMods = $this->getViewModes();
        //FieldTypeView.php
        if(is_null($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getFormComponent($this,$record,$parameter);
    }
    public function getInfolistComponent(CustomFieldAnswer $record,string $viewMode = "default", array $parameter = []): \Filament\Infolists\Components\Component{
        $viewMods = $this->getViewModes();
        //FieldTypeView.php
        if(is_null($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this,$record,$parameter);
        return ($viewMods[$viewMode])::getFormComponent($this,$record,$parameter);
    }

    public abstract function viewModes():array;

    public function getViewModes(?string $dynamicFormConfiguration = null):array {
        if(is_null($dynamicFormConfiguration)) return $this->viewModes();
        $viewMods = $this->viewModes();

        //Config Overwrite
        $overWrittenLevelOne = $this->overwriteViewModes();
        foreach (array_keys($overWrittenLevelOne) as $viewMode) $viewMods[$viewMode] = $overWrittenLevelOne[$viewMode];

        // Form Overwritten
        if(!is_null($dynamicFormConfiguration)){
            $overWrittenLevelTwo = ($dynamicFormConfiguration)->overwriteViewModes();
            foreach (array_keys($overWrittenLevelTwo) as $viewMode) $viewMods[$viewMode] = $overWrittenLevelOne[$viewMode];
        }

        return $viewMods;

    }

    public function overwriteViewModes():array{
        $viewModes = config("ffhs_custom_forms.view_modes");
        $overWritten = $viewModes[$this::class];
        if(isEmpty($overWritten)) return [];
        return $overWritten;
    }






    public function getTranslatedName():string{
        return __("custom_forms.types." . self::fieldIdentifier());
    }

    public static abstract function getFieldIdentifier():string;



    public static function getToolTips(CustomFieldVariation $record) :?string{
        return   $record->customField->getInheritState()["tool_tip_" . App::currentLocale()];
    }
    public static function getIdentifyKey(CustomFieldVariation $record) :string{
        return   $record->customField->getInheritState()["identify_key"];
    }
    public static function getLabelName(CustomFieldVariation $record) :string{
        return  $record->customField->getInheritState()["name_" . App::currentLocale()];
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


    public static function prepareCloneOptions(array $templateOptions, bool $isInheritGeneral) :array{
        return $templateOptions;
    }






}
