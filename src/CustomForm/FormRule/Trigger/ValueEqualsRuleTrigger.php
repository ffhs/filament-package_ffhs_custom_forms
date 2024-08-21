<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Lang;

class ValueEqualsRuleTrigger extends FormRuleTriggerType
{
    use HasFormTargets;

    public static function identifier(): string {
        return "value_equals_anchor";
    }


    public function isTrigger(array $arguments, mixed $target, RuleTrigger $rule): bool
    {
        if(!key_exists("answerer_state",$arguments)) return false;

    }

    public function getDisplayName(): string
    {
        return "Selber Wert";
    }

    public function getFormSchema(): array
    {
        return [
            $this->getTargetSelect()
                ->label("Feld"), //ToDo Translate
            ToggleButtons::make("type")
                ->options(["number", "text", "boolean"])
                ->nullable(false)
                ->hiddenLabel()
                ->required()
                ->grouped()
                ->live(),
            $this->getTextTypeGroup()
                ->hidden(fn($get) => $get("type") != "text"),
            $this->getNumberTypeGroup()
                ->hidden(fn($get) => $get("type") != "number"),
            $this->getBooleanTypeGroup()
                ->hidden(fn($get) => $get("type") != "boolean"),
        ];
    }
    private function getTextTypeGroup(): Component
    {
        return Group::make([

        ]);
    }
    private function getNumberTypeGroup(): Component
    {
        return Group::make([

        ]);
    }
    private function getBooleanTypeGroup(): Component
    {
        return Group::make([

        ]);
    }

    /*
    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string {
        $componentState = $component->getState();

        $valueState = array_values($componentState);
        $ruleKeyPosition = array_search($ruleData, $valueState);
        $ruleKey = array_keys($componentState)[$ruleKeyPosition];

        $getPrefix = "rules.".$ruleKey.".anchor_data.";
        $targetFieldData = self::getSelectedFieldData($get,$component, $getPrefix);

        if(is_null($targetFieldData)) return "can't find field"; //ToDo translate

        $targetFieldType= self::getFieldType($targetFieldData);

        if($targetFieldType instanceof CustomOptionType)
            return $this->getCustomOptionDisplayName($ruleData, $targetFieldData);


        switch ($ruleData["anchor_data"]["field_type"]){
            case "text":
                return $this->getTextDisplayName($ruleData, $targetFieldData);
            case "numeric":
                return $this->getNumericDisplayName($ruleData, $targetFieldData);;
            case "boolean":
                return $this->getBooleanDisplayName($ruleData, $targetFieldData);
            default:
                return parent::getDisplayName($ruleData, $component, $get);
        }
    }

    private function getNumericDisplayName($ruleData, $targetFieldData): string {
        $targetFieldName = $this->getFieldName($targetFieldData);

        $numericData = $ruleData["anchor_data"]["numeric"];
        if($numericData["exactly_number"]) return $targetFieldName." = " . $numericData["number"];

        $output = "";

        $greaterThan = $numericData["greater_than"];
        if(!empty($greaterThan)){
            $output .= $greaterThan;
            if($numericData["greater_equals"]) $output .= " <= ";
            else $output .= " < ";
        }

        $output .= $targetFieldName;

        $smallerThan = $numericData["smaller_than"];
        if(!empty($smallerThan)){
            if($numericData["smaller_equals"]) $output .= " >= ";
            else $output .= " > ";
            $output .= $smallerThan;
        }

        return $output;
    }

    private function getBooleanDisplayName($ruleData, $targetFieldData): string {
        $targetFieldName = $this->getFieldName($targetFieldData);
        if($ruleData["anchor_data"]["boolean"]) return $targetFieldName." wahr ist";
        return $targetFieldName." unwahr ist";
    }

    private function getTextDisplayName($ruleData, $targetFieldData): string {
        $targetFieldName = $this->getFieldName($targetFieldData);

        $cleanedValues = [];
        foreach ($ruleData["anchor_data"]["values"] as $value) $cleanedValues[] = "'".$value."'";

        if (sizeof($cleanedValues) == 1)
            return $targetFieldName." entspricht ".array_values($cleanedValues)[0];
        return $targetFieldName." entspricht [".implode(", ", $cleanedValues)."]";
    }

    private function getCustomOptionDisplayName($ruleData, $targetFieldData): string {
        $targetFieldName = $this->getFieldName($targetFieldData);

        $selectedOptions = $ruleData["anchor_data"]["selected_options"];
        $localisation = Lang::locale();
        $selectedOptionsName = [];

        if(empty($targetFieldData["general_field_id"])){
            foreach ($targetFieldData["options"]["customOptions"] as $optionData) {
                $identifier = $optionData["identifier"];
                if (!in_array($identifier, $selectedOptions)) continue;
                $selectedOptionsName[$identifier] = $optionData['name'][$localisation];
            }
        }
        else{
                    /**@var GeneralField $genField*/ /*
        $genField =  GeneralField::cached($targetFieldData["general_field_id"]);
            //GeneralField's
        foreach ($genField->customOptions as $option) {
        $identifier = $option->identifier;
        if (!in_array($identifier, $selectedOptions)) continue;
        $selectedOptionsName[$identifier] = $option->name;
        }
        }


        if (sizeof($selectedOptionsName) == 1)
            return $targetFieldName." ist ".array_values($selectedOptionsName)[0];
        return $targetFieldName." in [".implode(", ", $selectedOptionsName)."]";
        }
             */

}
