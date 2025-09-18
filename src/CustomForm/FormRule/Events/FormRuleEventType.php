<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleEvent;
use Ffhs\FfhsUtils\Contracts\Rules\EventType;
use Ffhs\FfhsUtils\Traits\Rules\IsEventType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Support\Components\Component;

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
        EmbedRuleEvent $rule,
        CustomField $target,
        array $arguments = [],
    ): CustomField {
        return $target;
    }

    public function handleAfterRenderForm(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = [],
    ): Component {
        return $target;
    }

    public function handleAfterRenderEntry(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = [],
    ): Component {
        return $target;
    }

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data;
    }

    public function handlerBeforeRun(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = [],
    ): mixed { //Todo ????
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

    public function subHandlerRun(
        EmbedRuleEvent $rule,
        mixed $target,
        array $arguments = [],
    ): mixed { //ToDo fix
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

    public function handleAnswerLoadMutation(
        EmbedRuleEvent $rule,
        mixed $target,
        array $arguments = [],
    ): mixed {
        return $target;
    }

    public function handleAnswerSaveMutation(
        EmbedRuleEvent $rule,
        mixed $target,
        array $arguments = [],
    ): mixed {
        return $target;
    }
}
