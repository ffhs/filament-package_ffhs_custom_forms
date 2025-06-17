<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsType;
use Filament\Forms\Components\Component;
use Illuminate\Support\Collection;


abstract class FormRuleEventType implements EventType
{
    use IsType;

    public static function getConfigTypeList(): string
    {
        return 'rule.event';
    }

    public function handle(Closure $triggers, array $arguments, mixed &$target, RuleEvent $rule): mixed
    {
        switch ($arguments['action']) {
            case 'before_render':
                return $this->handlerBeforeRun($triggers, $arguments, $target, $rule);;
            //case 'mutate_parameters': return $this->handleParameterMutation($triggers, $arguments, $target, $rule);
            case 'after_render':
                if (is_array($target) && (array_values($target)[0] ?? '') instanceof Component) {
                    $handler = $this->handleAfterRenderForm(...);
                } else {
                    $handler = $this->handleAfterRenderInfolist(...);
                }

                return $this->subHandlerRun($handler, $triggers, $arguments, $target, $rule);

            case 'load_answer':
                return $this->handleAnswerLoadMutation($triggers, $arguments, $target, $rule);
            case 'save_answer':
                return $this->handleAnswerSaveMutation($triggers, $arguments, $target, $rule);

            default:
                return null;
        }
    }

//    public function handleParameterMutation(Closure $triggers, array $arguments, array $parameters, RuleEvent $rule): array
//    {
//        return $parameters;
//    }

    public function getCustomField($arguments): ?CustomField
    {
        $identifier = $arguments['identifier'];
        return $arguments['custom_fields']->get($identifier);
    }

    public function handleBeforeRender(
        Closure $triggers,
        array $arguments,
        CustomField &$target,
        RuleEvent $rule
    ): CustomField {
        return $target;
    }

    public function handleAfterRenderForm(
        Closure $triggers,
        array $arguments,
        Component &$component,
        RuleEvent $rule
    ): Component {
        return $component;
    }

    public function handleAfterRenderInfolist(
        Closure $triggers,
        array $arguments,
        \Filament\Infolists\Components\Component &$component,
        RuleEvent $rule
    ): \Filament\Infolists\Components\Component {
        return $component;
    }

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data;
    }

    private function handlerBeforeRun(Closure $triggers, array $arguments, Collection $target, RuleEvent $rule): mixed
    {
        return $target->map(function (CustomField $item) use ($rule, $triggers) {
            if ($item instanceof CustomField) {
                $identifier = $item;
            }
            $modifiedTrigger = function (array $extraOptions = []) use ($identifier, $triggers) {
                return $triggers(array_merge(['target_field_identifier' => $identifier], $extraOptions));
            };
            $arguments['identifier'] = $identifier;
            return $this->handleBeforeRender($modifiedTrigger, $arguments, $item, $rule);
        });
    }

    private function subHandlerRun(
        Closure $subFunction,
        Closure $triggers,
        array $arguments,
        array &$target,
        RuleEvent $rule
    ): mixed {

        foreach ($target as $identifier => $item) {
            /**@var CustomField|Component|\Filament\Infolists\Components\Component $item */
            //dump($identifier, $item);
            $modifiedTrigger = function (array $extraOptions = []) use ($identifier, $triggers) {
                return $triggers(array_merge(['target_field_identifier' => $identifier], $extraOptions));
            };
            $arguments['identifier'] = $identifier;
            $target[$identifier] = $subFunction($modifiedTrigger, $arguments, $item, $rule);
        }

        return $target;
    }

    private function handleAnswerLoadMutation(
        Closure $triggers,
        array $arguments,
        mixed &$target,
        RuleEvent $rule
    ): mixed {
        return $target;
    }

    private function handleAnswerSaveMutation(
        Closure $triggers,
        array $arguments,
        mixed &$target,
        RuleEvent $rule
    ): mixed {
        return $target;
    }

}
