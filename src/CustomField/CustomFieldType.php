<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;



use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isEmpty;

abstract class CustomFieldType
{

    public static function getAllTypes():array{
        $output = [];
        foreach(config("ffhs_custom_forms.custom_field_types") as $typeClass){
            $output[$typeClass::getFieldName()]= $typeClass;
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


    public function getTranslatedName():string{
        return __("filament-package_ffhs_custom_forms::custom_forms.fields.types." . self::getObjFieldName());
    }

    public static abstract function getFieldName():string;

    public abstract function getFormComponent(CustomField $record,string $viewMode = "default", array $parameter = []): Component;
    public abstract function getViewComponent(CustomFieldAnswer $record,string $viewMode = "default", array $parameter = []): \Filament\Infolists\Components\Component;

    public static function getToolTips(CustomField $record) {
        return   $record->getInheritState()["tool_tip_" . App::currentLocale()];
    }
    public static function getLabelName(CustomField $record) {
        return  $record->getInheritState()["short_title_" . App::currentLocale()];
    }

    public function getObjFieldName():string{
        return $this::getFieldName();
    }

    public function getExtraOptionSchema():?array{
        return null;
    }

    public function getExtraOptionFields():array{
        return [];
    }


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
        return Cache::remember("has_extra_option-" .$this->getObjFieldName(),30,fn()=> !isEmpty($this->getExtraOptionSchema()));
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

    /*public static function prepareCloneOptions(array $templateOptions, bool $isInheritGeneral) :array{
        return $templateOptions;
    }*/


    public function getGeneralFieldExtraField(): ?Component{
        return  null;
    }



}
