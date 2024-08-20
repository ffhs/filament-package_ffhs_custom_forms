<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Illuminate\Support\Collection;


abstract class FieldRuleEventType implements EventType
{
     use IsType;


     public static function getConfigTypeList(): string
     {
        return "rules.events";
     }

    public function handle(bool $triggered, array $arguments, mixed $target, Rule $rule): mixed
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

    public function handleBeforeRender(bool $triggered, array $arguments, CustomField $target, Rule $rule): CustomField
    {
        return $target;
    }
    public function handleParameterMutation(bool $triggered, array $arguments, array $parameters, Rule $rule): array
    {
        return $parameters;
    }


    public function handleAfterRenderForm(bool $triggered, array $arguments, Component $component, Rule $rule): Component
    {
        return $component;
    }

    public function handleAfterRenderInfolist(bool $triggered, array $arguments, \Filament\Infolists\Components\Component $component, Rule $rule): \Filament\Infolists\Components\Component
    {
        return $component;
    }

    private function handleAfterAllRendered(bool $triggered, array $arguments, Collection $target, Rule $rule):Collection
    {
        return $target;
    }

}
