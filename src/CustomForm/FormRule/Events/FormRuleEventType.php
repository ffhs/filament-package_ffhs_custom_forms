<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
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

    public function handle(bool $triggered, array $arguments, mixed $target, RuleEvent $rule): mixed
    {
        switch ($arguments["action"]) {
            case "before_render": return $this->handleBeforeRender($triggered, $arguments, $target, $rule);
            case "mutate_parameters": return $this->handleParameterMutation($triggered, $arguments, $target, $rule);

            case "after_render": if($target instanceof Component)
                return $this->handleAfterRenderForm($triggered, $arguments, $target, $rule);
            else
                return $this->handleAfterRenderInfolist($triggered, $arguments, $target, $rule);

            case "after_all_rendered": return $this->handleAfterAllRendered($triggered, $arguments, $target, $rule);
            default: return null;
        }
    }


    public function getCustomField($arguments): CustomField
    {
        return $arguments["customField"];
    }

    public function handleBeforeRender(bool $triggered, array $arguments, CustomField $target, RuleEvent $rule): CustomField
    {
        return $target;
    }
    public function handleParameterMutation(bool $triggered, array $arguments, array $parameters, RuleEvent $rule): array
    {
        return $parameters;
    }


    public function handleAfterRenderForm(bool $triggered, array $arguments, Component $component, RuleEvent $rule): Component
    {
        return $component;
    }

    public function handleAfterRenderInfolist(bool $triggered, array $arguments, \Filament\Infolists\Components\Component $component, RuleEvent $rule): \Filament\Infolists\Components\Component
    {
        return $component;
    }

    private function handleAfterAllRendered(bool $triggered, array $arguments, Collection $target, RuleEvent $rule):Collection
    {
        return $target;
    }

}
