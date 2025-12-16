<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleEvent;
use Ffhs\FfhsUtils\Contracts\Rules\RuleTriggersCallback;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Support\Components\Component;

abstract class  IsPropertyOverwriteEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasTriggerEventFormTargets;
    use CanLoadFormAnswer;

    public function handleAfterRenderFormComponent(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = []
    ): Component {
        if (!in_array($arguments['identifier'] ?? '', $rule->data['targets'] ?? [], false)) {
            return $target;
        }

        return $this->prepareComponent($target, $rule->getRule()->getTriggersCallback($target, $arguments));
    }

    public function handleAfterRenderEntryComponent(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = []
    ): Component {
        if (!in_array($arguments['identifier'] ?? '', $rule->data['targets'] ?? [], false)) {
            return $target;
        }

        return $this->prepareComponent($target, $rule->getRule()->getTriggersCallback($target, $arguments));
    }

    public function getConfigurationSchema(): array
    {
        return [$this->getTargetsSelect()];
    }

    protected function prepareComponent(Component $component, RuleTriggersCallback $triggers): Component
    {
        $property = $this->property();
        //Access the protected Property from the object $component, $this is than $component
        $oldProperty = (fn() => $this->$property)->call($component);

        $propertyFunction = $this->getPropertyFunction($oldProperty, $triggers);

        //Set the protected Property from the object $component, $this is than $component
        (fn() => $this->$property = $propertyFunction)->call($component);

        return $component;
    }

    protected function getPropertyFunction(mixed $oldProperty, RuleTriggersCallback $triggers): Closure
    {
        return fn(Component $component) => once(function () use ($component, $oldProperty, $triggers) {
            if (!$component instanceof \Filament\Schemas\Components\Component) {
                return $component->evaluate($oldProperty);
            }

            $triggered = $triggers(); //todo fuck... what if with repeaters

            if ($triggered !== $this->dominatingSide()) {
                $triggered = $component->evaluate($oldProperty);
            }

            return $triggered;
        });
    }

    abstract protected function property(): string;

    abstract protected function dominatingSide(): bool;
}
