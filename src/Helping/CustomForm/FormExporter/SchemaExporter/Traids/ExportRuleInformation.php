<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Support\Collection;

trait ExportRuleInformation
{

    public function exportRuleInformation(Collection $rules): array
    {
        return $rules->map(function (Rule $rule) {
            return [
                'is_or_mode' => $rule->is_or_mode,
                'triggers' => $this->exportTriggers($rule->ruleTriggers),
                'events' => $this->exportEvents($rule->ruleEvents),
            ];
        })->toArray();
    }

    private function exportTriggers(Collection $triggers): array
    {
        return $triggers->map(function (RuleTrigger $trigger) {
            return [
                'is_inverted' => $trigger->is_inverted,
                'type' => $trigger->type,
                'data' => $trigger->data,
            ];
        })->toArray();
    }

    private function exportEvents(Collection $events): array
    {
        return $events->map(function (RuleEvent $event) {
            return [
                'type' => $event->type,
                'data' => $event->data,
            ];
        })->toArray();
    }

}
