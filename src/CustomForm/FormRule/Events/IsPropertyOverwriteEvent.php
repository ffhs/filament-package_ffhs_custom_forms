<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfolistComponent;
use ReflectionClass;

abstract class  IsPropertyOverwriteEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasTriggerEventFormTargets;
    use CanLoadFormAnswer;


    public function handleAfterRenderForm(
        Closure $triggers,
        array $arguments,
        Component &$component,
        RuleEvent $rule
    ): Component {
        if (!in_array($arguments['identifier'], $rule->data['targets'], false)) {
            return $component;
        }
        return $this->prepareComponent($component, $triggers);
    }

    public function handleAfterRenderInfolist(
        Closure $triggers,
        array $arguments,
        InfolistComponent &$component,
        RuleEvent $rule
    ): InfolistComponent {
        if (empty($rule->data)) {
            return $component;
        }
        if (empty($rule->data['targets'])) {
            return $component;
        }

        $getCustomField = $this->getCustomField($arguments);
        if (is_null($getCustomField)) {
            return $component;
        }
        $customFieldId = $getCustomField->identifier;

        $inTargets = in_array($customFieldId, $rule->data['targets'], false);
        return $inTargets
            ? $this->prepareComponent($component, $triggers)
            : $component;
    }

    public function getFormSchema(): array
    {
        return [$this->getTargetsSelect()];
    }

    /**
     * @throws \ReflectionException
     */
    protected function prepareComponent(Component|InfolistComponent $component, $triggers): Component|InfolistComponent
    {
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty($this->property());
        $oldProperty = $property->getValue($component);

        if ($component instanceof Component) {
            $propertyFunction = $this->getPropertyFormFunction($oldProperty, $triggers);
        } else {
            $propertyFunction = $this->getPropertyInfolistFunction($oldProperty, $triggers);
        }

        $property->setValue($component, $propertyFunction);
        return $component;
    }

    protected function getPropertyFormFunction(mixed $oldProperty, $triggers): Closure
    {
        return fn(Component $component) => once(function () use ($component, $oldProperty, $triggers) {
            $triggered = $triggers(['state' => $component->getGetCallback()('.')]);
            if ($triggered !== $this->dominatingSide()) {
                $triggered = $component->evaluate($oldProperty);
            }
            return $triggered;
        });
    }

    protected function getPropertyInfolistFunction(mixed $oldProperty, $triggers): Closure
    {
        return function (InfolistComponent $component, CustomFormAnswer $record) use ($oldProperty, $triggers) {
            $state = $record->loadedData();
            $triggered = $triggers(['state' => $state]);
            if ($triggered !== $this->dominatingSide()) {
                $triggered = $component->evaluate($oldProperty);
            }
            return $triggered;
        };
    }

    abstract protected function property(): string;

    abstract protected function dominatingSide(): bool;
}
