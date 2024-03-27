<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Type;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\HasRulePluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Component as InfoComponent;
use ReflectionClass;

class ChangeOptionRuleType extends FieldRuleType
{
    use HasRulePluginTranslate;
    public static function identifier(): string {
        return "change_options_rule";
    }

    public function canAddOnField(CustomFieldType $type): bool {
        return $type instanceof CustomOptionType;
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Select::make("customOptions")
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
    public function afterRender(Component|InfoComponent $component, CustomField $customField, FieldRule $rule): Component|InfoComponent {
        if(!in_array(HasOptions::class,class_uses_recursive($component::class))) return $component;
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("options");
        $property->setAccessible(true);
        $optionsOld = $property->getValue($component);

        return $component->options(function ($get,$set) use ($optionsOld, $customField, $component, $rule) {
            if(!$rule->getAnchorType()->canRuleExecute($component,$customField,$rule)) $options= $component->evaluate($optionsOld);
            else{
                $customField->customOptions =  $customField->customOptions->whereIn("identifier",$rule->rule_data["customOptions"]);
                $options =  FormMapper::getAvailableCustomOptions($customField);
            }
            $currentValue = $get(FormMapper::getIdentifyKey($customField));
            if(!array_key_exists($currentValue,$options->toArray())) $set(FormMapper::getIdentifyKey($customField), null);
            return $options;
        });


    }


}
