<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use BackedEnum;
use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleEvent;
use Ffhs\FfhsUtils\Contracts\Rules\EventType;
use Ffhs\FfhsUtils\Traits\Rules\IsEventType;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;

abstract class FormRuleEventType implements EventType
{
    use IsEventType {
        IsEventType::handle as handleEvent;
    }


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
    public function handle(
        BackedEnum|string $action,
        EmbedRuleEvent $ruleEvent,
        mixed &$target,
        array $arguments = [],
    ): mixed {

        return match ($action) {
            FormRuleAction::AfterRenderForm => $this->handleAfterRenderForm($ruleEvent, $target, $arguments),
            FormRuleAction::AfterRenderEntry => $this->handleAfterRenderEntry($ruleEvent, $target, $arguments),
            default => $this->handleEvent($action, $ruleEvent, $target, $arguments)
        };
    }


    public function getCustomField($arguments): ?CustomField
    {
        $identifier = $arguments['identifier'] ?? '';

        return ($arguments['custom_fields'] ?? collect())->get($identifier);
    }

    public function handleLoadData(EmbedRuleEvent $rule, mixed $data, array $arguments = []): mixed
    {
        return $data;
    }

    public function handleBeforeRender(EmbedRuleEvent $rule, Collection $data, array $arguments = []): Collection
    {
        return $data;
    }

    public function handleAfterRenderForm(EmbedRuleEvent $event, array $target, array $arguments = []): array
    {
        foreach ($target as $identifier => $component) {
            /**@var Component $component */
            $arguments['identifier'] = $identifier;
            $target[$identifier] = $this->handleAfterRenderFormComponent($event, $component, $arguments);
        }

        return $target;
    }

    public function handleAfterRenderEntry(
        EmbedRuleEvent $event,
        array $target,
        array $arguments = [],
    ): array {
        foreach ($target as $identifier => $component) {
            /**@var Component $component */
            $arguments['identifier'] = $identifier;
            $target[$identifier] = $this->handleAfterRenderEntryComponent($event, $component, $arguments);
        }

        return $target;
    }

    public function handleAfterRenderFormComponent(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = [],
    ): Component {
        return $target;
    }

    public function handleAfterRenderEntryComponent(
        EmbedRuleEvent $rule,
        Component $target,
        array $arguments = [],
    ): Component {
        return $target;
    }

    public function mutateDataOnClone(array $data, CustomForm $target): array
    {
        return $data; //ToDo????
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
