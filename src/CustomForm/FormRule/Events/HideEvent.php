<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\HasFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Filament\Forms\Components\Component;
use ReflectionClass;
use Filament\Infolists\Components\Component as InfolistComponent;

class HideEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasFormTargets;

    public static function identifier(): string {
        return "hidden_event";
    }



    public function handleAfterRenderForm(Closure $triggered, array $arguments, Component $component, RuleEvent $rule): Component
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->identifier;
        if(in_array($customFieldId, $rule->data["targets"])) return $this->setUpComponent($component, $arguments, $triggered);
        else return $component;
    }

    public function handleAfterRenderInfolist(Closure $triggered, array $arguments,InfolistComponent  $component, RuleEvent $rule): InfolistComponent
    {
        if(empty($rule->data)) return $component;
        if(empty($rule->data["targets"])) return $component;

        $customFieldId = $this->getCustomField($arguments)->toArray()["identifier"];

        if(in_array($customFieldId, $rule->data["targets"])) return $this->setUpComponent($component, $arguments, $triggered);
        else return $component;
    }


    public function getFormSchema(): array
    {
        return [$this->getTargetsSelect()];
    }


    protected function setUpComponent(Component|InfolistComponent $component, $arguments, $triggers): Component|InfolistComponent
    {
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty("isHidden");
        $property->setAccessible(true);
        $isHiddenOld = $property->getValue($component);
        $customField = $this->getCustomField($arguments);

        $hiddenFunction = function (Component|InfolistComponent $component, mixed $set) use ($customField, $isHiddenOld, $triggers) {
            $hidden =  $triggers();
            if (!$hidden) $hidden = $component->evaluate($isHiddenOld);
            if($hidden && !is_null($set)) $set($customField->identifier, null);
            return $hidden;
        };

        if($component instanceof Component)
            return $component->hidden(fn ($component, $set) => $hiddenFunction($component, $set));
        else
            return $component->hidden(fn ($component) => $hiddenFunction($component, null));
    }
}
