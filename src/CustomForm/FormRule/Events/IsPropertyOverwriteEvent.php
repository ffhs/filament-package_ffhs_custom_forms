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

    public function handleAfterRenderForm(EmbedRuleEvent $rule, Component $target, array $arguments = []): Component
    {
        if (!in_array($arguments['identifier'] ?? '', $rule->data['targets'] ?? [], false)) {
            return $target;
        }

        return $this->prepareComponent($target, $rule->getRule()->getTriggersCallback($target, $arguments));
    }


    public function handleAfterRenderEntry(EmbedRuleEvent $rule, Component $target, array $arguments = []): Component
    {
        if (!in_array($arguments['identifier'] ?? '', $rule->data['targets'] ?? [], false)) {
            return $target;
        }

        return $this->prepareComponent($target, $rule->getRule()->getTriggersCallback($target, $arguments));
        /*
        if (empty($rule->data) || empty($rule->data['targets'])) {
            return $target;
        }

        $customField = $this->getCustomField($arguments);

        if (is_null($customField)) {
            return $target;
        }

        $customFieldId = $customField->identifier;
        $inTargets = in_array($customFieldId, $rule->data['targets'], false);

        return $inTargets
            ? $this->prepareComponent($target, $rule->getRule()->getTriggersCallback($target, $arguments))
            : $target;*/
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
                return static fn() => $component->evaluate($oldProperty);
            }

            $triggered = $triggers(['state' => $component->makeGetUtility()('.')]);

            if ($triggered !== $this->dominatingSide()) {
                $triggered = $component->evaluate($oldProperty);
            }

            return $triggered;
        });
    }

    /*protected function getPropertyInfolistFunction(mixed $oldProperty, $triggers): Closure
    {
        return function (Component $component, CustomFormAnswer $record) use ($oldProperty, $triggers) {
            $state = $record->loadedData(); //FFFF
            $triggered = $triggers(['state' => $state]);

            if ($triggered !== $this->dominatingSide()) {
                $triggered = $component->evaluate($oldProperty);
            }

            return $triggered;
        };
    }
     */


    abstract protected function property(): string;

    abstract protected function dominatingSide(): bool;

}
