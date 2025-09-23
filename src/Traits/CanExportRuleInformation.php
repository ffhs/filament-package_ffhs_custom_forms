<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Illuminate\Support\Collection;

trait CanExportRuleInformation
{
    public function exportRuleInformation(Collection $rules): array
    {
        return $rules
            ->map(fn(Rule $rule) => [
                'is_or_mode' => $rule->is_or_mode,
                'triggers' => $this->exportTriggers($rule->ruleTriggers),
                'events' => $this->exportEvents($rule->ruleEvents),
            ])
            ->toArray();
    }

    private function exportTriggers(Collection $triggers): array
    {
        return $triggers
            ->map(fn(RuleTrigger $trigger) => [
                'is_inverted' => $trigger->is_inverted,
                'type' => $trigger->type,
                'data' => $trigger->data,
            ])
            ->toArray();
    }

    private function exportEvents(Collection $events): array
    {
        return $events
            ->map(fn(RuleEvent $event) => [
                'type' => $event->type,
                'data' => $event->data,
            ])
            ->toArray();
    }
}
