<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\old\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Infolists\Components\Component as InfoComponent;
use Illuminate\Support\Facades\Lang;
use ReflectionClass;

class ChangeOptionRuleType extends FieldRuleAbstractType
{
    use HasRuleTriggerPluginTranslate;
    public static function identifier(): string {
        return "change_options_rule";
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return $type instanceof CustomOptionType;
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string { //ToDo translate

        $localisation = Lang::locale();
        $fieldName = $get("name_" . $localisation);
        $selectedOptions = $ruleData["rule_data"]["customOptions"];

        $selectedOptionsName = [];
        $options = $get("options.customOptions");

        foreach ($options as $optionData) {
            $identifier = $optionData["identifier"];
            if (!in_array($identifier, $selectedOptions)) continue;
            $selectedOptionsName[$identifier] = $optionData["name_".$localisation];
        }

        return $fieldName." Auswahlmöglichkeiten [".implode(", ", $selectedOptionsName)."]";
    }


    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Select::make("customOptions")
            ->label("Anzuzeigende Optionen")
            ->multiple()
            ->options(function ($component){
                $field = array_values($component->getLivewire()->getCachedForms())[1]->getRawState();
                if(array_key_exists("general_field_id",$field) && !is_null($field["general_field_id"])){
                    $genField = GeneralField::cached($field["general_field_id"]);
                    if(!array_key_exists("options",$field)) $field["options"] = [];
                    if(!array_key_exists("customOptions",$field["options"])) $field["customOptions"] = [];
                    $options = $field["options"]["customOptions"];
                    $genOptions = $genField->customOptions->whereIn("id", $options);

                    return $genOptions->pluck("name_de","identifier"); //ToDo translate
                }
                if(!array_key_exists("options",$field)) $field["options"] = [];
                if(!array_key_exists("customOptions",$field["options"])) $field["customOptions"] = [];
                $options = $field["options"]["customOptions"];
                return  collect($options)->pluck("name_de","identifier");
            });
    }


    public function getCreateRuleData(): array {
        return [
            "customOptions" => []
        ];
    }
    public function afterComponentRender(Component|InfoComponent $component,  FieldRule $rule): Component|InfoComponent {
        if(!in_array(HasOptions::class,class_uses_recursive($component::class))) return $component;
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("options");
        $property->setAccessible(true);
        $optionsOld = $property->getValue($component);
        $customField = $rule->customField;

        return $component->options(function ($get,$set) use ($optionsOld, $customField, $component, $rule) {
            $anchorDecisions = $this->canRuleExecute($component,$rule);
            if(!$anchorDecisions) $options= $component->evaluate($optionsOld);
            else{
                $customField->customOptions =  $customField->customOptions->whereIn("identifier",$rule->rule_data["customOptions"]);
                $options =  FieldMapper::getAvailableCustomOptions($customField);
            }
            $currentValue = $get(FieldMapper::getIdentifyKey($customField));
            if(!array_key_exists($currentValue,$options->toArray())) $set(FieldMapper::getIdentifyKey($customField), null);
            return $options;
        });


    }


}
