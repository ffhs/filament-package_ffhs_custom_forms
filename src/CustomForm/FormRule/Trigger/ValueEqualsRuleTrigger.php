<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ValueEqualsRuleTrigger extends FormRuleTriggerType
{
    use HasFormTargets;

    public static function identifier(): string {
        return "value_equals_anchor";
    }

    public function prepareComponent(Component|\Filament\Infolists\Components\Component $component, RuleTrigger $trigger): Component|\Filament\Infolists\Components\Component
    {
        if($component instanceof Component)
            return $component->live();
        else return $component;
    }


    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        if(!key_exists("state",$arguments)) return false;

        if(empty($rule->data)) return false;
        if(empty($rule->data["target"])) return false;
        if(empty($rule->data["type"])) return false;

        $targetFieldIdentifyer = $rule->data["target"];
        $state = $arguments["state"];
        $targetValue = $state[$targetFieldIdentifyer] ?? null;
        $type = $rule->data["type"];

        return match ($type) {
            "number" => $this->checkNumber($targetValue, $rule->data),
            "text" => $this->checkText($targetValue, $rule->data),
            "boolean" => $this->checkBoolean($targetValue, $rule->data),
            "null" => $this->checkNull($targetValue),
            "option" => $this->checkOption($targetValue, $rule->data),
            default => false,
        };

    }

    public function getDisplayName(): string
    {
        return "Bestimmter Wert"; //ToDo Tra
    }

    public function getFormSchema(): array
    {
        return [
            $this->getTargetSelect()
                ->label("Feld"), //ToDo Translate
            ToggleButtons::make("type")
                ->options(fn() => [
                    "number" => "Nummer",
                    "text" => "Text",
                    "boolean" => "Boolean",
                    "null" => "Leer",
                    "option" => "Optionen"
                ])
               ->disableOptionWhen(function($value, $get) { //make Better
                    if($value != "option") return false;
                    $target = $get("target");
                    $formState = $get("../../../../../custom_fields")??[];
                    $customField = [];
                    foreach ($formState as $field) {
                        $customField = new CustomField();
                        $customField->fill($field);
                        if($customField->identifier() != $target) $customField = null;
                        else break;
                    }


                    if(empty($customField)) return true;
                    return !($customField->getType() instanceof CustomOptionType);
                })
                ->afterStateUpdated(function ($get, $set){
                     switch ($get("type")) {
                        case"text":
                            $set("values",[]);
                            break;
                        case"option":
                            $set("selected_options",[]);
                            break;
                    }
                })
                ->nullable(false)
                ->hiddenLabel()
                ->required()
                ->grouped()
                ->live(),
            $this->getTextTypeGroup()
                ->visible(fn($get) => $get("type") == "text")
                ->live(),
            $this->getNumberTypeGroup()
                ->visible(fn($get) => $get("type") == "number")
                ->live(),
            $this->getBooleanTypeGroup()
                ->visible(fn($get) =>  $get("type") == "boolean")
                ->live(),
            $this->getOptionTypeGroup()
                ->visible(fn($get) =>  $get("type") == "option")
                ->live(),
        ];
    }
    protected function getTextTypeGroup(): Component
    {
        return Group::make([
            TagsInput::make("values")
                ->reorderable(false)
                ->columnSpanFull()
                ->label("")
        ]);
    }
    protected function checkText(mixed $targetValue, array $data): bool
    {
        if(!is_string($targetValue)) return false;
        if(empty($data["values"])) return false;

        foreach($data["values"] as $value){
            if(fnmatch($value, $targetValue)) return true;
        }

        return false;
    }


    protected function getNumberTypeGroup(): Component
    {
        return Group::make([

            Checkbox::make("exactly_number")
                ->label("Genaue Nummer")
                ->columnSpanFull()
                ->live(),

            TextInput::make("number")
                ->prefixIcon("carbon-character-whole-number")
                ->visible(fn($get)=> $get("exactly_number"))
                ->label("Nummer")
                ->required()
                ->numeric(),

            Group::make()
                ->hidden(fn($get)=> $get("exactly_number"))
                ->columns(5)
                ->columnSpanFull()
                ->schema([

                    Hidden::make("greater_equals")
                        ->live(),
                    Hidden::make("smaller_equals")
                        ->live(),

                    TextInput::make("greater_than")
                        ->label("Grösser als") //ToDo Translate
                        ->suffixAction(Action::make("greater_equals_action")
                            ->action(fn($set, $get) => $set("greater_equals", !($get("greater_equals")??false)))
                            ->color(Color::hex("#000000"))
                            ->icon(fn($get)=> $get("greater_equals")?"tabler-math-equal-lower":"tabler-math-lower")
                        )
                        ->columnStart(1)
                        ->columnSpan(2)
                        ->numeric(),
                    Placeholder::make("")
                        ->content(fn()=>new HtmlString(Blade::render("<div class='flex flex-col items-center justify-center'><br><x-bi-input-cursor style='height: auto; width: 40px'/></div>"))) //ToDo Translate
                        ->label(" "),
                    TextInput::make("smaller_than")
                        ->prefixAction(Action::make("smaller_than_action")
                            ->action(fn($set, $get) => $set("smaller_equals", !($get("smaller_equals")??false)))
                            ->color(Color::hex("#000000"))
                            ->icon(fn($get)=> $get("smaller_equals")?"tabler-math-equal-greater":"tabler-math-greater")
                        )
                        ->label("Kleiner als") //ToDo Translate
                        ->columnStart(4)
                        ->columnSpan(2)
                        ->numeric(),
                ]),
            Placeholder::make("")
                ->content(fn()=>"Feld leer lassen, damit keine Abfrage ausgeführt wird") //ToDo Translate
                ->columnSpanFull()
                ->label(""),
        ]);
    }
    protected function getBooleanTypeGroup(): Component
    {
        return Group::make([
            Toggle::make("boolean")
                ->label("Wert") //ToDo Translate
                ->columnSpanFull()
        ]);
    }

    protected function checkBoolean(mixed $targetValue, array $data): bool
    {
        if(is_null($targetValue)) return false;
        if(!empty($data["boolean"])) $boolean = $data["boolean"];
        else $boolean = false;
        return $targetValue == $boolean;
    }
    protected function checkNumber(mixed $targetValue, array $data): bool
    {
        $value = floatval($targetValue);
        if($data["exactly_number"]) return $data[" number"] == $value;

        if(!empty($data["greater_than"])){
            if($data["greater_equals"] && !($value >= $data["greater_than"])) return false;
            if(!$data["greater_equals"] && !($value > $data["greater_than"])) return false;
        }

        if(!empty($data["smaller_than"])){
            if($data["smaller_equals"] && !($value <= $data["smaller_than"])) return false;
            if(!$data["smaller_equals"] && !($value < $data["smaller_than"])) return false;
        }

        return true;
    }

    protected function getOptionTypeGroup(): Component
    {
        return  Select::make("selected_options")
            ->columnSpanFull()
            ->multiple()
            ->options(function ($get, CustomForm $record) {
                $finalField = self::getTargetFieldData($get);
                if(is_null($finalField)) return [];


                if(array_key_exists("general_field_id",$finalField) && !is_null($finalField["general_field_id"])){
                    //GeneralFields
                    $genField = (new CustomField())->fill($finalField)->generalField;

                    if(!array_key_exists("options",$finalField)) return [];
                    if(!array_key_exists("customOptions",$finalField["options"])) return [];
                    $options = collect($finalField["options"]["customOptions"]);

                    return $genField->customOptions
                        ->whereIn("id",$options)
                        ->pluck("name","identifier")
                        ->toArray();
                }else{
                    if(!array_key_exists("options",$finalField)) return [];
                    if(!array_key_exists("customOptions",$finalField["options"])) return [];
                    $options = collect($finalField["options"]["customOptions"]);
                    return $options->pluck("name." . $record->getLocale(),"identifier");
                }

            });
    }

    protected function checkOption(mixed $targetValue, array $data): bool|int
    {
        if(empty($data)) return false;
        if(empty($data['selected_options'])) return false;

        //Custom Option Types like Select
        $options =  $data['selected_options'];
        if(is_null($targetValue)) return false;
        if(is_array($targetValue)) return sizeof(array_intersect($targetValue,$options));
        return in_array($targetValue,$options);
    }

    private function checkNull(mixed $targetValue): bool
    {
        if(is_null($targetValue)) return true;
        if(is_bool($targetValue)) return false;
        if($targetValue == "0") return false;
        return empty($targetValue);
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
