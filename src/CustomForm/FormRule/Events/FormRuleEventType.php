<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use Illuminate\Support\Collection;


abstract class FormRuleEventType implements EventType
{
     use IsType;


     public static function getConfigTypeList(): string
     {
        return "rule.event";
     }

    public function handle(Closure $triggers, array $arguments, mixed &$target, RuleEvent $rule): mixed
    {


        switch ($arguments["action"]) {
            case "before_render":
                return $this->supHandlerRun($this->handleBeforeRender(...), $triggers, $arguments, $target, $rule);
               ;
            //case "mutate_parameters": return $this->handleParameterMutation($triggers, $arguments, $target, $rule);
            case "after_render":
                if(is_array($target) && (array_values($target)[0]??"") instanceof Component) {
                    $answare = $this->supHandlerRun($this->handleAfterRenderForm(...), $triggers, $arguments, $target, $rule);
                }
                else{
                    $answare = $this->supHandlerRun($this->handleAfterRenderInfolist(...), $triggers, $arguments, $target, $rule);
                }
                return $answare;

            case "load_answerer": return $this->handleAnswerLoadMutation($triggers, $arguments, $target, $rule);
            case "save_answerer": return $this->handleAnswerSaveMutation($triggers, $arguments, $target, $rule);


            default: return null;
        }
    }

//    public function handleParameterMutation(Closure $triggers, array $arguments, array $parameters, RuleEvent $rule): array
//    {
//        return $parameters;
//    }

    private function supHandlerRun(Closure $supFunction, Closure $triggers, array $arguments, array|Collection &$target, RuleEvent $rule): mixed
    {
        if($target instanceof Collection){
            return $target->map(function (CustomField $item) use ($rule, $triggers, $supFunction) {
                if($item instanceof CustomField) $identifier = $item;
                $modifiedTrigger = function (array $extraOptions = []) use ($identifier, $item, $triggers) {
                    return $triggers(array_merge(["target_field_identifier" => $identifier], $extraOptions ));
                };
                $arguments["identifier"] = $identifier;
                return $supFunction($modifiedTrigger, $arguments, $item, $rule);
            });
        }

        foreach ($target as $identifier => $item){
            /**@var CustomField|Component|\Filament\Infolists\Components\Component $item*/

            $modifiedTrigger = function (array $extraOptions = []) use ($identifier, $item, $triggers) {
                return $triggers(array_merge(["target_field_identifier" => $identifier], $extraOptions ));
            };
            $arguments["identifier"] = $identifier;
            $target[$identifier]  = $supFunction($modifiedTrigger, $arguments, $item, $rule);
        }

        return $target;
    }

    private function handleAnswerLoadMutation(Closure $triggers, array $arguments, mixed &$target, RuleEvent $rule): mixed    {
        return $target;
    }

    private function handleAnswerSaveMutation(Closure $triggers, array $arguments, mixed &$target, RuleEvent $rule): mixed
    {
        return $target;
    }

    public function getCustomField($arguments): CustomField
    {
        $identifier = $arguments["identifier"];
        $fields = $arguments["custom_fields"][$identifier];
        return  $fields;
    }

    public function handleBeforeRender(Closure $triggers, array $arguments, CustomField &$target, RuleEvent $rule): CustomField
    {
        return $target;
    }

    public function handleAfterRenderForm(Closure $triggers, array $arguments, Component &$component, RuleEvent $rule): Component
    {
        return $component;
    }

    public function handleAfterRenderInfolist(Closure $triggers, array $arguments, \Filament\Infolists\Components\Component &$component, RuleEvent $rule): \Filament\Infolists\Components\Component
    {
        return $component;
    }

    public function mutateDataOnClone(array $data, CustomForm $target):array{
         return $data;
    }

}
