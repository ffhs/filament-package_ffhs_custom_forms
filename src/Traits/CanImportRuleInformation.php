<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FfhsUtils\Models\\Rule;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Ffhs\FfhsUtils\Models\RuleTrigger;

trait CanImportRuleInformation
{
    public function importRule(array $rawDataRule, CustomForm $customForm): void
    {
        $rules = [];

        foreach ($rawDataRule as $rule) {
            $isOrMode = $rule['is_or_mode'] ?? false;
            $rule = new Rule(['is_or_mode' => $isOrMode]);
            $rules[] = $rule;
        }

        $customForm
            ->ownedRules()
            ->saveMany($rules);
        $count = 0;

        foreach ($rawDataRule as $ruleRaw) {
            $this->importRuleElements($rules[$count], $ruleRaw);
            $count++;
        }
    }

    public function importRuleElements(Rule $rule, array $rawRuleData): void
    {
        $events = $this->mapRuleElements($rawRuleData['events'] ?? [], RuleEvent::class);
        $triggers = $this->mapRuleElements($rawRuleData['triggers'] ?? [], RuleTrigger::class);

        $rule
            ->ruleEvents()
            ->saveMany($events);
        $rule
            ->ruleTriggers()
            ->saveMany($triggers);
    }

    private function mapRuleElements(array $elements, string $class): array
    {
        $order = 0;

        return array_map(function ($element) use (&$order, $class) {
            $order++;

            return new $class([
                ...$element,
                'order' => $order - 1,
            ]);
        }, $elements);
    }
}
