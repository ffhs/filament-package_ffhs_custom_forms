<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\FormRule\Translations\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfolistComponent;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

abstract class  IsPropertyOverwriteEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasTriggerEventFormTargets;


    public function handleAfterRenderForm(
        Closure $triggers,
        array $arguments,
        Component &$component,
        RuleEvent $rule
    ): Component {
        if (!in_array($arguments["identifier"], $rule->data["targets"])) return $component;
        return $this->prepareComponent($component, $triggers);
    }

    protected function prepareComponent(Component|InfolistComponent $component, $triggers): Component|InfolistComponent
    {
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty($this->property());
        $property->setAccessible(true);
        $oldProperty = $property->getValue($component);


        if ($component instanceof Component) {
            $hiddenFunction = function (Component $component) use ($oldProperty, $triggers) {
                $triggered = $triggers(["state" => $component->getGetCallback()(".")]);
                if ($triggered != $this->dominatingSide()) {
                    $triggered = $component->evaluate($oldProperty);
                }
                // if($hidden && !is_null($set)) $set($customField->identifier, null);
                return $triggered;
            };
        } else {
            $hiddenFunction = function (InfolistComponent $component, CustomFormAnswer $record) use (
                $oldProperty,
                $triggers
            ) {
                $state = Cache::remember(
                    $record->id . "custom_form_answare_state_load_infolist",
                    2,
                    fn() => CustomFormLoadHelper::load($record)
                );
                $triggered = $triggers(["state" => $state]);
                if ($triggered != $this->dominatingSide()) {
                    $triggered = $component->evaluate($oldProperty);
                }
                // if($hidden && !is_null($set)) $set($customField->identifier, null);
                return $triggered;
            };
        }

        $property->setValue($component, $hiddenFunction);
        return $component;
    }

    protected abstract function property(): string;

    protected abstract function dominatingSide(): bool;

    public function handleAfterRenderInfolist(
        Closure $triggers,
        array $arguments,
        InfolistComponent &$component,
        RuleEvent $rule
    ): InfolistComponent {
        if (empty($rule->data)) return $component;
        if (empty($rule->data["targets"])) return $component;

        $getCustomField = $this->getCustomField($arguments);
        if (is_null($getCustomField)) return $component;
        $customFieldId = $getCustomField->identifier;

        $inTargets = in_array($customFieldId, $rule->data["targets"]);
        return $inTargets
            ? $this->prepareComponent($component, $triggers)
            : $component;
    }

    public function getFormSchema(): array
    {
        return [$this->getTargetsSelect()];
    }
}
