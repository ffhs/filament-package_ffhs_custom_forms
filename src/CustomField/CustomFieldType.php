<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;



use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
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

    public function getFormComponent(CustomField $record, string $viewMode = "default", array $parameter = []): \Filament\Forms\Components\Component{
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
        $overWrittenLevelOne = $this->getOverwriteViewModes();
        foreach (array_keys($overWrittenLevelOne) as $viewMode) $viewMods[$viewMode] = $overWrittenLevelOne[$viewMode];

        // Form Overwritten
        if(!is_null($dynamicFormConfiguration)){
            $overWrittenLevelTwo = ($dynamicFormConfiguration)->getOverwriteViewModes();
            foreach (array_keys($overWrittenLevelTwo) as $viewMode) $viewMods[$viewMode] = $overWrittenLevelOne[$viewMode];
        }

        return $viewMods;

    }

    public function getOverwriteViewModes():array{
        $viewModes = config("ffhs_custom_forms.view_modes");
        $overWritten = $viewModes[$this::class];
        if(isEmpty($overWritten)) return [];
        return $overWritten;
    }






    public function getTranslatedName():string{
        return __("custom_forms.types." . self::fieldIdentifier());
    }

    public static abstract function getFieldIdentifier():string;

    public static function getToolTips(CustomField $record) {
        return   $record->getInheritState()["tool_tip_" . App::currentLocale()];
    }
    public static function getLabelName(CustomField $record) {
        return  $record->getInheritState()["short_title_" . App::currentLocale()];
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

    /*
     ToDo:
    public function prepareOptionDataBeforeFill(array $data):array{
         if(!array_key_exists("field_options",$data) || is_null($data["field_options"]) )$data["field_options"] = ["options"=> []];
         else $data["field_options"] = ["options"=>$data["field_options"]];
         return $data;
    }
    public function prepareOptionDataBeforeSave(?array $data):array{
        if(array_key_exists("field_options",$data)&&!is_null($data["field_options"]) && !empty($data["field_options"]))
            $data["field_options"] =array_values($data["field_options"])[0];
        else $data["field_options"] = null;
        return $data;
    }

    public function prepareOptionDataBeforeCreate(array $data):array{
        return $this->prepareOptionDataBeforeSave($data);
    }

    public function hasExtraOptions():bool{
        return Cache::remember("has_extra_option-" .$this->getObjFieldIdentifier(),30,fn()=> !isEmpty($this->getExtraOptionSchema()));
    }

    public function getExtraOptionsRepeater(): ?Repeater{
        if(!$this->hasExtraOptions()) return null;
        return Repeater::make("field_options")
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

    public static function prepareCloneOptions(array $templateOptions, bool $isInheritGeneral) :array{
        return $templateOptions;
    }*/






}
