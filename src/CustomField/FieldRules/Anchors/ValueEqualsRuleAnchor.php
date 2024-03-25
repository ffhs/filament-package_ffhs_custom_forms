<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasAnchorPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;

class ValueEqualsRuleAnchor extends FieldRuleAnchorType
{
    use HasAnchorPluginTranslate;


    public static function identifier(): string {
        return "value_equals_anchor";
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

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {

        return Group::make()
            ->columnSpanFull()
            ->columns()
            ->schema([
                ToggleButtons::make("target_field")
                   // ->required() ToDo Fix
                    ->columns()
                    ->live()
                    ->options(function ($component) use ($fieldData) {
                        $fieldData = $this->mapFieldData($fieldData);
                        $thisField = array_values($component->getLivewire()->getCachedForms())[1]->getRawState();
                        if(array_key_exists("identify_key",$thisField)) $identifyKey = $thisField["identify_key"];
                        else $identifyKey = null;

                        $options = [];
                        foreach ($fieldData as $field){
                            if(!array_key_exists("identify_key",$field)) continue;
                            if($identifyKey == $field["identify_key"]) continue;
                            $isGeneralField = !empty($field["general_field_id"]);
                            $type = CustomFormEditForm::getFieldTypeFromRawDate($field);
                            if($type instanceof CustomLayoutType) continue;
                            $options[$field["identify_key"]] = ($isGeneralField? "* ":"") .$field["name_de"]; //ToDo Translate
                        }

                        return $options;
                    }),
                Select::make("field_type")
                    ->selectablePlaceholder(false)
                    // ->required() ToDo Fix
                    //->nullable(false) ToDo Fix
                    ->label("Feldtypen")
                    ->live()
                    ->afterStateUpdated(function($state,$set){
                        $set("values", null);
                        $set("value", null);
                    })
                    ->options([//ToDo Translate
                               "text" => "Text",
                               "boolean" => "Ja/Nein",
                               "custom_option" => "Optionen",
                    ]),


                Toggle::make("value")
                    ->visible(fn($get)=> $get("field_type") == "boolean")
                    ->columnSpanFull()
                    ->label("Wert"),
                Repeater::make("values")
                    ->visible(fn($get) => $get("field_type") == "text")
                    ->columnSpanFull()
                    ->label("")
                    ->schema([
                        TextInput::make("value")
                            ->label("Wert")
                            ->required(),
                    ]),
                Select::make("values")
                    ->visible(fn($get) => $get("field_type") == "custom_option")
                    ->columnSpanFull()
                    ->multiple()
                    ->options(function ($get, Select $component) use ($customForm):array {
                        $identifier = $get("target_field");
                        if(is_null($identifier)) return [];
                        $fields = array_values($component->getLivewire()->getCachedForms())[0]->getRawState()["custom_fields"];
                        $finalField = null;

                        foreach ($fields as $field){
                            if(array_key_exists("general_field_id",$field) && !is_null($field["general_field_id"])){
                                $genField = GeneralField::cached($field["general_field_id"]);
                                if($genField?->identify_key != $identifier) continue;
                                $finalField = $field;
                                break;
                            }
                            if($field["identify_key"] != $identifier) continue;
                            $finalField = $field;
                            break;
                        }

                        //ToDo get Options identifyer and Translated Name

                        if(array_key_exists("general_field_id",$finalField) && !is_null($finalField["general_field_id"])){
                            //GeneralFields
                            $genField = GeneralField::cached($finalField["general_field_id"]);
                            return $genField->customOptions->pluck("name_de","identifier")->toArray();
                        }else{
                            if(!array_key_exists("options",$finalField)) return [];
                            if(!array_key_exists("customOptions",$finalField["options"])) return [];
                            $options = collect($finalField["options"]["customOptions"]);
                            return $options->pluck("name_de","identifier")->toArray();
                        }

                    })
            ]);
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
        $type = $rule->anchor_data["field_type"];
        if($type == "bool") return $formState[$target] == $rule->anchor_data["value"];
        if($type == "text") {
            $options = $this->flatten($rule->anchor_data["values"]);
            $options = array_values($options);
            return  in_array($formState[$target],$options);
        }
        if($type == "custom_option") {
            $options = $rule->anchor_data["values"];
            return  in_array($formState[$target],$options);
        }
        else return false;
    }
}
