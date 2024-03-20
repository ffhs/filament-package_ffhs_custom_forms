<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\ToggleButtons;

class ValueEqualsRuleAnchor extends FieldRuleAnchorType
{

    public function shouldRuleExecute(CustomFormAnswer $formAnswer, CustomFieldAnswer $fieldAnswer, FieldRule $rule): bool {
        return true;
    }

    private function mapFieldData(array $fieldDatas):array {
        $output = [];
        foreach ($fieldDatas as $fieldKey => $fieldData){
            if(!array_key_exists("custom_fields",$fieldData)) {
                $output[$fieldKey] =$fieldData;
                continue;
            }
            $output =  array_merge($this->mapFieldData($fieldData["custom_fields"]), $output);
        }

        return $output;
    }

    public function createComponent(CustomForm $customForm, array $fieldData): Component {

        $options = [];
        $fieldData = $this->mapFieldData($fieldData);

        foreach ($fieldData as $field){
            if(!array_key_exists("identify_key",$field)) continue;
            $isGeneralField = !empty($field["general_field_id"]);
            $type = CustomFormEditForm::getFieldTypeFromRawDate($field);
            if($type instanceof CustomLayoutType) continue;
            $options[$field["identify_key"]] = ($isGeneralField? "* ":"") .$field["name_de"]; //ToDo Translate
        }

        return Group::make()
            ->schema([
                ToggleButtons::make("target_field")
                    ->options($options),
            ]);
    }

    public static function identifier(): string {
       return "value_equals_anchor";
    }
}
