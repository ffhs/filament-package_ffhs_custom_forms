<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleEvent;
use Filament\Support\Components\Component;

class DisableOptionsEvent extends OptionsEvent
{
    protected static string $identifier = 'disable_options_rule';

    public function handleAfterRenderFormComponent(
        EmbedRuleEvent $rule,
        mixed $target,
        array $arguments = []
    ): Component {
        $identifier = $arguments['identifier'] ?? 'none';

        if ($identifier !== ($rule->data['target'] ?? '')) {
            return $target;
        }

        $trigger = $rule->getRule()->getTriggersCallback($target, $arguments);

        return $target->disableOptionWhen(function ($get, $value) use ($trigger, $rule) {
            $triggered = once(fn(): bool => $trigger(['state' => $get('.')]));

            return $triggered && in_array($value, $rule->data['selected_options'] ?? [], true);
        });
    }

}
