<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use ReflectionClass;
use Filament\Infolists\Components\Component as InfolistComponent;

abstract class  IsPropertyOverwriteEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasFormTargets;


    protected abstract function property(): string;
    protected abstract function dominatingSide(): bool;

    public function handleAfterRenderForm(Closure $triggers, array $arguments, Component $component, RuleEvent $rule): Component
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->identifier;
        if(in_array($customFieldId, $rule->data["targets"])) return $this->prepareComponent($component, $triggers);
        else return $component;
    }

    public function handleAfterRenderInfolist(Closure $triggers, array $arguments,InfolistComponent  $component, RuleEvent $rule): InfolistComponent
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->toArray()["identifier"];

        if(in_array($customFieldId, $rule->data["targets"])) return $this->prepareComponent($component, $triggers);
        else return $component;
    }


    public function getFormSchema(): array
    {
        return [$this->getTargetsSelect()];
    }


    protected function prepareComponent(Component|InfolistComponent $component, $triggers): Component|InfolistComponent
    {
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty($this->property());
        $property->setAccessible(true);
        $oldProperty = $property->getValue($component);

        $hiddenFunction = function (Component|InfolistComponent $component) use ($oldProperty, $triggers) {
            $triggered = $triggers(["state" => $component->getGetCallback()(".")]);
            if ($triggered == $this->dominatingSide()) $triggered = $component->evaluate($oldProperty);
            // if($hidden && !is_null($set)) $set($customField->identifier, null);
            return $triggered;
        };
        $property->setValue($component, $hiddenFunction);
        return $component;
    }
}
