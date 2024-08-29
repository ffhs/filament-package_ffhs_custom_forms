<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use Illuminate\Support\Collection;


abstract class FormRuleEventType implements EventType
{
     use IsType;


     public static function getConfigTypeList(): string
     {
        return "rules.events";
     }

    public function handle(Closure $triggers, array $arguments, mixed $target, RuleEvent $rule): mixed
    {
        switch ($arguments["action"]) {
            case "before_render": return $this->handleBeforeRender($triggers, $arguments, $target, $rule);
            case "mutate_parameters": return $this->handleParameterMutation($triggers, $arguments, $target, $rule);

            case "after_render": if($target instanceof Component)
                return $this->handleAfterRenderForm($triggers, $arguments, $target, $rule);
            else
                return $this->handleAfterRenderInfolist($triggers, $arguments, $target, $rule);
            case "load_answerer": return $this->handleAnswerLoadMutation($triggers, $arguments, $target, $rule);
            case "save_answerer": return $this->handleAnswerSaveMutation($triggers, $arguments, $target, $rule);

            case "after_all_rendered": return $this->handleAfterAllRendered($triggers, $arguments, $target, $rule);
            default: return null;
        }
    }


    public function getCustomField($arguments): CustomField
    {
        return $arguments["customField"];
    }

    public function handleBeforeRender(Closure $triggers, array $arguments, CustomField $target, RuleEvent $rule): CustomField
    {
        return $target;
    }
    public function handleParameterMutation(Closure $triggers, array $arguments, array $parameters, RuleEvent $rule): array
    {
        return $parameters;
    }


    public function handleAfterRenderForm(Closure $triggers, array $arguments, Component $component, RuleEvent $rule): Component
    {
        return $component;
    }

    public function handleAfterRenderInfolist(Closure $triggers, array $arguments, \Filament\Infolists\Components\Component $component, RuleEvent $rule): \Filament\Infolists\Components\Component
    {
        return $component;
    }

    private function handleAfterAllRendered(Closure $triggers, array $arguments, Collection $target, RuleEvent $rule):Collection
    {
        return $target;
    }

    private function handleAnswerLoadMutation(Closure $triggers, array $arguments, mixed $target, RuleEvent $rule): mixed    {
        return $target;
    }

    private function handleAnswerSaveMutation(Closure $triggers, array $arguments, mixed $target, RuleEvent $rule): mixed
    {
        return $target;
    }

}
