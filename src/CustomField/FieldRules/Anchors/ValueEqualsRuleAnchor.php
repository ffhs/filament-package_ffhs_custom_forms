<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;

class ValueEqualsRuleAnchor extends FieldRuleAnchorType
{



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

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {

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
            ->columnSpanFull()
            ->columns()
            ->schema([
                ToggleButtons::make("target_field")
                    ->options($options)
                    ->columns(),
                TextInput::make("value")
                    ->label("Betrag"),
            ]);
    }

    public static function identifier(): string {
       return "value_equals_anchor";
    }

    function flatten($array): array {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, $this->flatten($value));
            } else {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    public function shouldRuleExecute(array $formState, CustomField $customField, FieldRule $rule): bool {
        $formState = $this->flatten($formState);
        $target = $rule->anchor_data["target_field"];
        if(!array_key_exists($target, $formState)) return false;
        return $formState[$target] == $rule->anchor_data["value"];// $rule->anchor_data["custom_fields"];
    }
}
