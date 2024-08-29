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
                        ->pluck("name", "identifier")
                 ),
             Select::make("customOptions")
                 ->label("Anzuzeigende Optionen")
                 ->multiple()
                 ->hidden(function ($set, $get){
                     //Fields with an array doesn't generate properly
                     if($get('customOptions') == null)
                         $set("customOptions",[]);
                 })
                 ->options(function ($get, CustomForm $record){
                     $field = $this->getTargetFieldData($get);

                     if(empty($field)) return [];

                     $customField = new CustomField();
                     $customField->fill($field);

                     if($customField->isGeneralField()){
                         $genOptions = $customField->generalField->customOptions;
                         return $genOptions->pluck("name","identifier");
                     }


                     if(!array_key_exists("options",$field)) $field["options"] = [];
                     if(!array_key_exists("customOptions",$field["options"])) $field["options"]["customOptions"] = [];
                     $options = $field["options"]["customOptions"];
                     return  collect($options)->pluck("name.". $record->getLocale(),"identifier");
                 })
         ];
     }

     public function handleAfterRenderForm(Closure $triggers, array $arguments, Component $component, RuleEvent $rule): Component
     {
         if(!in_array(HasOptions::class,class_uses_recursive($component::class))) return $component;
         $reflection = new ReflectionClass($component);
         $property = $reflection->getProperty("options");
         $property->setAccessible(true);
         $optionsOld = $property->getValue($component);
         $customField = $this->getCustomField($arguments);

         return $component->options(function ($get,$set) use ($triggers, $optionsOld, $customField, $component, $rule) {
             $triggerd = $triggers(["state" => $get(".")]);


             if(!$triggerd) $options = $component->evaluate($optionsOld);
             else{
                 $customOptions =  $customField->customOptions->whereIn("identifier",$rule->data["customOptions"]);
                 $customField->setCacheValue("customOptions",$customOptions );
                 $options =  FieldMapper::getAvailableCustomOptions($customField);
             }

             $currentValue = $get(FieldMapper::getIdentifyKey($customField));
             if(!array_key_exists($currentValue,$options->toArray())) $set(FieldMapper::getIdentifyKey($customField), null);
             return $options;
         });

     }


 }
