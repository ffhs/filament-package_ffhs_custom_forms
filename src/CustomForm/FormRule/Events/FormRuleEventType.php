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

    public function handle(
        BackedEnum|string $action,
        EmbedRuleEvent $ruleEvent,
        mixed &$target,
        array $arguments = [],
    ): mixed {

        return match ($action) {
            FormRuleAction::AfterRenderForm => $this->handleAfterRender($ruleEvent, $target, $arguments, false),
            FormRuleAction::AfterRenderEntry => $this->handleAfterRender($ruleEvent, $target, $arguments, true),
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

    protected function handleAfterRender(EmbedRuleEvent $event, array $target, array $arguments, bool $isEntry): array
    {
        foreach ($target as $identifier => $component) {
            /**@var Component $component */
            $arguments['identifier'] = $identifier;

            if ($isEntry) {
                $target[$identifier] = $this->handleAfterRenderEntryComponent($event, $component, $arguments);
            } else {
                $target[$identifier] = $this->handleAfterRenderFormComponent($event, $component, $arguments);
            }
        }

        return $target;
    }
}
