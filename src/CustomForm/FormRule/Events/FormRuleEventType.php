<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Closure;
use Ffhs\FfhsUtils\Contracts\Rules\EmbedRule;
use Ffhs\FfhsUtils\Contracts\Rules\EventType;
use Ffhs\FfhsUtils\Contracts\Rules\RuleTriggersCallback;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Ffhs\FfhsUtils\Traits\Rules\IsEventType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;

abstract class FormRuleEventType implements EventType
{
    use IsEventType;


    //ToDo fix

    /*
     *   public function handle(
        RuleTriggersCallback $triggers,
        array $arguments,
        mixed &$target,
        EmbedRuleEvent $rule
    ): mixed {

        switch ($arguments['action']) {
            case 'before_render':
                return $this->handlerBeforeRun($triggers, $arguments, $target, $rule);
            //case 'mutate_parameters': return $this->handleParameterMutation($triggers, $arguments, $target, $rule);
            case 'after_render': //ToDo FUCK
                if (is_array($target) && (array_values($target)[0] ?? '') instanceof FormsComponent) {
                    $handler = $this->handleAfterRenderForm(...);
                } else {
                    $handler = $this->handleAfterRenderEntry(...);
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
     */

    public function getCustomField($arguments): ?CustomField
    {
        $identifier = $arguments['identifier'] ?? '';

        return ($arguments['custom_fields'] ?? collect())->get($identifier);
    }

    public function handleBeforeRender(
        Closure $triggers,
        array $arguments,
        CustomField $target,
        RuleEvent $rule
    ): CustomField {
        return $target;
    }

    public function handleAfterRenderForm(
        EmbedRule $rule,
        Component $target,
        array $arguments = [],
    ): Component {
        return $target;
    }

    public function handleAfterRenderEntry(
        RuleTriggersCallback $triggers,
        array $arguments,
        Component &$component,
        RuleEvent $rule
    ): Component {
        return $component;
    }

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data;
    }

    private function handlerBeforeRun(
        RuleTriggersCallback $triggers,
        array $arguments,
        Collection $target,
        RuleEvent $rule
    ): mixed {
        return $target->map(function (CustomField $item) use ($rule, $triggers) {
            if ($item instanceof CustomField) {
                $identifier = $item;
            }

            $modifiedTrigger = fn(array $extraOptions = []) => $triggers(
                array_merge(['target_field_identifier' => $identifier], $extraOptions)
            );
            $arguments['identifier'] = $identifier;

            return $this->handleBeforeRender($modifiedTrigger, $arguments, $item, $rule);
        });
    }

    private function subHandlerRun(
        Closure $subFunction,
        RuleTriggersCallback $triggers,
        array $arguments,
        array &$target,
        RuleEvent $rule
    ): mixed {
        foreach ($target as $identifier => $item) {
            /**@var CustomField|Component $item */
            //dump($identifier, $item);
            $modifiedTrigger = fn(array $extraOptions = []) => $triggers(
                array_merge(['target_field_identifier' => $identifier], $extraOptions)
            );
            $arguments['identifier'] = $identifier;
            $target[$identifier] = $subFunction($modifiedTrigger, $arguments, $item, $rule);
        }

        return $target;
    }

    private function handleAnswerLoadMutation(
        RuleTriggersCallback $triggers,
        array $arguments,
        mixed &$target,
        RuleEvent $rule
    ): mixed {
        return $target;
    }

    private function handleAnswerSaveMutation(
        RuleTriggersCallback $triggers,
        array $arguments,
        mixed &$target,
        RuleEvent $rule
    ): mixed {
        return $target;
    }
}
