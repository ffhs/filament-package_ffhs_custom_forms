<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Illuminate\Support\Collection;

abstract class  CustomOptionType extends CustomFieldType
{

    public function getExtraTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionOption(false),
        ];
    }

    public function getExtraGeneralTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionOption(true),
        ];
    }




    public function getAvailableCustomOptions(CustomField $record) : Collection{
        if($record->isGeneralField()){
            $options = $record->customOptions;
            return $options->pluck("name_de","identifier");//ToDo Translate
        }
        return collect(FormMapper::getOptionParameter($record,"customOptions"))->pluck("name_de","identifier");//ToDo Translate
    }

    public function getAllCustomOptions(CustomField|CustomFieldAnswer $record) : Collection{
        if($record instanceof CustomFieldAnswer) $record = $record->customField;
        if($record->isInheritFromGeneralField()) $options = $record->generalField->customOptions;
        else $options = collect(FormMapper::getOptionParameter($record,"customOptions")) ;
        return $options->pluck("name_de","identifier");//ToDo Translate
    }




    /*
    public static function prepareCloneOptionsOLD(array $variationData, string $target, $set, Get $get) :array{
        if(!empty($get("general_field_id"))) return $variationData["options"];

        $customOptions = $variationData["customOptions"] ;
        foreach($customOptions as $key => $option) unset($customOptions[$key]["id"]);

        $set($target.".customOptions",$customOptions);
        return $variationData["options"];
    }*/





}
