<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;

abstract class  CustomOptionType extends CustomFieldType
{

    public function extraTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionTypeOption(false),
        ];
    }

    public function generalTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionTypeOption(true),
        ];
    }

    public function mutateOnTemplateDissolve(array $data, CustomField $original): array {
        if($original->isGeneralField()) return parent::mutateOnTemplateDissolve($data,$original);
        $options = [];
        foreach ($original->customOptions as $customOption){
            /**@var CustomOption $customOption*/
            $customOptionData = $customOption->toArray();
            unset($customOptionData["id"]);
            unset($customOptionData["created_at"]);
            unset($customOptionData["deleted_at"]);
            unset($customOptionData["updated_at"]);
            unset($customOptionData["pivot"]);
            $options[uniqid()] = $customOptionData;
        }
        $data["options"]["customOptions"] = $options;
        return parent::mutateOnTemplateDissolve($data,$original);
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
