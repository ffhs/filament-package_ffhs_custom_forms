<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use ReflectionClass;
use Filament\Infolists\Components\Component as InfolistComponent;

 class ChangeOptionsEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasFormTargets;

     public static function identifier(): string {
         return "change_options_rule";
     }

     public function getFormSchema(): array
     {
         return [
             $this->getTargetSelect()
                ->options(fn($get)=>
                   collect($this->getAllFieldsData($get))
                        ->map(fn($field) => (new CustomField())->fill($field))
                        ->filter(fn(CustomField $field)=> $field->getType() instanceof CustomOptionType)
                        ->map(fn(CustomField $field) => ["name" => $field->name, "identifier" => $field->identifier])
                        ->pluck("name", "identifier")
                 ),
             Select::make("selected_options")
                 ->label("Anzuzeigende Optionen")
                 ->multiple()
                 ->hidden(function ($set, $get){
                     //Fields with an array doesn't generate properly
                     if($get('selected_options') == null)
                         $set("selected_options",[]);
                 })
                 ->options(function ($get, CustomForm $record){
                     $field = $this->getTargetFieldData($get);

                     if(empty($field)) return [];

                     $customField = new CustomField();
                     $customField->fill($field);

                     if($customField->isGeneralField()){
                         $genOptions = $customField->generalField->customOptions;
                         $selectedOptions = $this->getTargetFieldData($get)["options"]["customOptions"] ?? [];
                         $genOptions = $genOptions->whereIn("id", $selectedOptions);
                         return $genOptions->pluck("name","identifier");
                     }


                     if(!array_key_exists("options",$field)) $field["options"] = [];
                     if(!array_key_exists("selected_options",$field["options"])) $field["options"]["selected_options"] = [];
                     $options = $field["options"]["selected_options"];
                     return  collect($options)->pluck("name.". $record->getLocale(),"identifier");
                 })
         ];
     }

     public function handleAfterRenderForm(Closure $triggers, array $arguments, Component $component, RuleEvent $rule): Component
     {
         $customField = $this->getCustomField($arguments);
         if($customField->identifier !== ($rule->data["target"] ?? "")) return $component;
         if(!in_array(HasOptions::class, class_uses_recursive($component::class))) return $component;

         $reflection = new ReflectionClass($component);
         $property = $reflection->getProperty("options");
         $property->setAccessible(true);
         $optionsOld = $property->getValue($component);


         return $component->options(function ($get,$set) use ($triggers, $optionsOld, $customField, $component, $rule) {
             $triggered = $triggers(["state" => $get(".")]);

             $options = $component->evaluate($optionsOld);
             if(!$triggered) return $options;
             if($options instanceof Collection) $options = $options->toArray();
             foreach ($options as $key => $option) {
                 if(in_array($key, $rule->data["selected_options"])) continue;
                 unset($options[$key]);
             }

             //Check to replace the current value
             $currentValue = $get(FieldMapper::getIdentifyKey($customField));
             if(is_array($currentValue)){
                 $diff = array_intersect($currentValue, array_keys($options));
                 if(sizeof($diff) != sizeof($currentValue)) $set($customField->identifier, $diff);
             }
             else if(!array_key_exists($currentValue, $options)) $set($customField->identifier, null);

             return $options;
         });

     }


 }
