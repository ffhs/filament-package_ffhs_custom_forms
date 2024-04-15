<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasAnchorPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorMutationHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ValueEqualsRuleAnchor extends FieldRuleAnchorType
{
    use HasAnchorPluginTranslate;


    public static function identifier(): string {
        return "value_equals_anchor";
    }

    private static function mapFieldData(array $fields):array {
        $finalField = [];
        foreach ($fields as $field){
            if(array_key_exists("custom_fields",$field))
                $finalField =array_merge($finalField, self::mapFieldData($field["custom_fields"]));
            unset($field["custom_fields"]);
            $finalField[] = $field;
        }
        return $finalField;
    }

    protected static function getSelectedFieldData(Get $get,Component $component):array|null {
        $identifier = $get("target_field");
        if(is_null($identifier)) return null;

        $fields = array_values($component->getLivewire()->getCachedForms())[0]->getRawState();

        //Flatt Array if CustomForm is in a sub path
        for($i=0; $i<=10; $i++){
            if(array_key_exists("custom_fields",$fields)) break;
            $fields = CustomFieldUtils::flattArrayOneLayer ($fields);
        }
        $fields = $fields["custom_fields"];
        $fields = self::mapFieldData($fields);

        //Get the templated FormComponents
        $fieldsFromTemplate = collect($fields)->whereNotNull("template_id")->map(function($templateData){

            $template = CustomForm::cached($templateData["template_id"]);
            return $template->customFields->map(function(CustomField $customField) use ($template) {
                $data = $customField->toArray();
                $data = CustomFormEditorMutationHelper::mutateOptionData($data, $template);
                return CustomFormEditorMutationHelper::mutateRuleDataOnLoad($data, $template);
            });
        })->flatten(1)->toArray();

        $fields = array_merge($fieldsFromTemplate, $fields);

        //Search the target field
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
        return $finalField;
    }

    protected static function getFieldType(array $fieldData): CustomFieldType {
        if(!array_key_exists("general_field_id",$fieldData) || is_null($fieldData["general_field_id"]))
            $fieldType = CustomFieldType::getTypeFromName($fieldData["type"]);
        else{
            $genField = GeneralField::cached($fieldData["general_field_id"]);
            $fieldType = $genField->getType();
        }
        return $fieldType;
    }


    protected function getTargetFieldToggleList(array $fieldData):ToggleButtons  {
        return ToggleButtons::make("target_field")
            ->required()
            ->columns()
            ->live()
            ->afterStateUpdated(function($state,$set){
                $set("values", self::getCreateAnchorData()["values"]);
                $set("selected_options", self::getCreateAnchorData()["selected_options"]);
                $set("numeric", self::getCreateAnchorData()["numeric"]);
                $set("boolean", self::getCreateAnchorData()["boolean"]);
            })
            ->options(fn($component) => $this->getFieldOptions($component,$fieldData) );
    }

    public function getFieldOptions(Component $component, array $fieldData): array {
        $fieldData = self::mapFieldData($fieldData);
        $thisField = array_values($component->getLivewire()->getCachedForms())[1]->getRawState();
        if(array_key_exists("identify_key",$thisField)) $identifyKey = $thisField["identify_key"];
        else $identifyKey = null;

        $options = [];
        foreach ($fieldData as $field){

            if(array_key_exists("identify_key",$field) && $identifyKey == $field["identify_key"])  continue;

            $isGeneralField = !empty($field["general_field_id"]);
            $isTemplate = !empty($field["template_id"]);
            $type = CustomFormEditorHelper::getFieldTypeFromRawDate($field);

            //Skip Layout Types
            if($type instanceof CustomLayoutType) continue;

            //Hande GeneralField
            if($isGeneralField){
                $field = GeneralField::cached($field["general_field_id"]);
                $options[$field->identify_key] =$field->name_de; //ToDo Translate
                continue;
            }

            //Hande Templates
            if($isTemplate){
                $template = CustomForm::cached($field["template_id"]);
                foreach ($template->customFields as $templateField){
                    /**@var CustomField $templateField*/
                    $finalField= $templateField;
                    if($templateField->isGeneralField()) $finalField = $templateField->generalField;
                    $options[$finalField->identify_key] =$finalField->name_de; //ToDo Translate
                }
                continue;
            }


            $options[$field["identify_key"]] = $field["name_de"]; //ToDo Translate
        }

        return $options;
    }

    protected function getFieldTypeSelect():Select  {
        return Select::make("field_type")
            //->selectablePlaceholder(false)
            ->required()
            ->label("Feldtypen")
            ->live()
            ->afterStateUpdated(function($state,$set){
                $set("values", self::getCreateAnchorData()["values"]);
                $set("numeric", self::getCreateAnchorData()["numeric"]);
                $set("boolean", self::getCreateAnchorData()["boolean"]);
            })
            ->options([//ToDo Translate
                       "text" => "Text",
                       "boolean" => "Ja/Nein",
                       "numeric" => "Nummer",
            ])
            ->visible(function($get,$component) {
                $fieldData = self::getSelectedFieldData($get,$component);
                if(is_null($fieldData)) return true;
                $fieldType= self::getFieldType($fieldData);
                return !($fieldType instanceof CustomOptionType);
            });
    }

    protected function getDisableCloser(string $selectOption): Closure {
        return function($get,$component) use ($selectOption) {
            $fieldData = self::getSelectedFieldData($get,$component);
            if(is_null($fieldData)) return false;
            $fieldType= self::getFieldType($fieldData);
            return !($fieldType instanceof CustomOptionType) && $get("field_type") == $selectOption;
        };
    }

    protected function getBooleanToggle():Toggle  {
        return  Toggle::make("boolean")
            ->visible($this->getDisableCloser("boolean"))
            ->label("Wert") //ToDo Translate
            ->columnSpanFull();
    }

    protected function getTextRepeater():Repeater  {
        return Repeater::make("values")
            ->visible($this->getDisableCloser("text"))
            ->columnSpanFull()
            ->label("")
            ->schema([
                TextInput::make("value")
                    ->label("Wert")
                    ->required(),
            ]);
    }

    protected function getOptionSelector():Select  {
        return  Select::make("selected_options")
            ->visible(function($get,$component) {
                $fieldData = self::getSelectedFieldData($get,$component);
                if(is_null($fieldData)) return false;
                $fieldType= self::getFieldType($fieldData);
                return $fieldType instanceof CustomOptionType;
            })
            ->columnSpanFull()
            ->multiple()
            ->options(function ($get, Select $component):array {
                $finalField = self::getSelectedFieldData($get,$component);
                if(is_null($finalField)) return [];

                if(array_key_exists("general_field_id",$finalField) && !is_null($finalField["general_field_id"])){
                    //GeneralFields
                    $genField = GeneralField::cached($finalField["general_field_id"]);

                    if(!array_key_exists("options",$finalField)) return [];
                    if(!array_key_exists("customOptions",$finalField["options"])) return [];
                    $options = collect($finalField["options"]["customOptions"]);

                    return $genField->customOptions->whereIn("id",$options)->pluck("name_de","identifier")->toArray(); //ToDo Translate
                }else{
                    if(!array_key_exists("options",$finalField)) return [];
                    if(!array_key_exists("customOptions",$finalField["options"])) return [];
                    $options = collect($finalField["options"]["customOptions"]);
                    return $options->pluck("name_de","identifier")->toArray(); //ToDo Translate
                }

            });
    }
    protected function getNumberSection(): Section {

        return Section::make()
            ->visible($this->getDisableCloser("numeric"))
            ->statePath("numeric")
            ->columnSpanFull()
            ->schema([
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
                        Placeholder::make("")
                            ->content(fn()=>"Feld leer lassen, damit keine Abfrage ausgeführt wird") //ToDo Translate
                            ->columnSpanFull()
                            ->label(""),

                        Checkbox::make("greater_equals")
                            ->label("Grösser GLEICH als") //ToDo Translate
                            ->columnStart(1)
                            ->columnSpan(2)
                            ->inline()
                            ->live(),
                        Checkbox::make("smaller_equals")
                            ->label("Kleiner GLEICH als") //ToDo Translate
                            ->columnStart(4)
                            ->columnSpan(2)
                            ->inline()
                            ->live(),

                        TextInput::make("greater_than")
                            ->suffix(fn($get)=> $get("greater_equals")?"<=":"<")
                            ->label("Grösser als") //ToDo Translate
                            ->columnStart(1)
                            ->columnSpan(2)
                            ->numeric(),
                        Placeholder::make("")
                            ->content(fn()=>new HtmlString(Blade::render("<div class='flex flex-col items-center justify-center'><br><x-bi-input-cursor style='height: auto; width: 40px'/></div>"))) //ToDo Translate
                            ->label(" "),
                        TextInput::make("smaller_than")
                            ->prefix(fn($get)=> $get("smaller_equals")?">=":">")
                            ->label("Kleiner als") //ToDo Translate
                            ->columnStart(4)
                            ->columnSpan(2)
                            ->numeric(),
                    ]),
            ]);
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {

        return Group::make()
            ->columnSpanFull()
            ->columns()
            ->schema([
                $this->getTargetFieldToggleList($fieldData),
                $this->getFieldTypeSelect(),

                //SpecificFields
                $this->getBooleanToggle(),
                $this->getTextRepeater(),
                $this->getNumberSection(),
                $this->getOptionSelector(),
            ]);
    }
    public function getCreateAnchorData(): array {
        return [
            "target_field"=> null,
            "field_type"=> "text",
            "boolean"=> false,
            "values"=> [],
            "selected_options"=>[],
            "numeric"=> [
                "exactly_number" => false,
                "number" => 0,
                "greater_equals" => false,
                "greater_than" => null,
                "smaller_equals" => false,
                "smaller_than" => null,

            ],
        ];
    }


    public function shouldRuleExecute(array $formState, CustomField $customField, FieldRule $rule): bool {
        $formState = CustomFieldUtils::flatten($formState);
        $target = $rule->anchor_data["target_field"];
        if(!array_key_exists($target, $formState)) return false;
        $type = $rule->anchor_data["field_type"];

        $customForm = $customField->customForm;
        $targetField = $customForm->cachedFieldsWithTemplates()->where("identify_key",$target)->first();
        if(is_null($targetField)) {
            $genField = GeneralField::query()->where("identify_key",$target)->select("id")->first();
            /**@var null|GeneralField $genField*/
            if(is_null($genField)) return false;
            $targetField = $customForm->customFields->where("general_field_id",$genField->id)->first();
        }
        if(is_null($targetField)) return false;
        $targetFieldType = $targetField->getType(); //ToDO Optimize

        //Custom Option Types like Select
        if($targetFieldType instanceof CustomOptionType) {
            $options = $rule->anchor_data["selected_options"];
            if(is_null($options)) return false;
            return in_array($formState[$target],$options);
        }

        //Bool
        if($type == "boolean")  return $formState[$target] == $rule->anchor_data["boolean"];

        //Text
        if($type == "text") {
            $options = CustomFieldUtils::flattenWithoutKeys($rule->anchor_data["values"]);
            $options = array_values($options);
            return  in_array($formState[$target],$options);
        }

        //Nummer
        if($type == "numeric") {
            $numericData = $rule->anchor_data["numeric"];
            $value = intval($formState[$target]);
            if($numericData["exactly_number"]) return $numericData["number"] == $value;

            if(!empty($numericData["greater_than"])){
                if($numericData["greater_equals"] && !($value >= $numericData["greater_than"])) return false;
                if(!$numericData["greater_equals"] && !($value > $numericData["greater_than"])) return false;
            }

            if(!empty($numericData["smaller_than"])){
                if($numericData["smaller_equals"] && !($value <= $numericData["smaller_than"])) return false;
                if(!$numericData["smaller_equals"] && !($value < $numericData["smaller_than"])) return false;
            }

            return true;
        }
        else return false;
    }



}
