<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;

trait ImportRuleInformation
{
    public function importRule(array $rawDataRule, CustomForm $customForm): void
    {
        $rules = [];
        foreach ($rawDataRule as $rule) {
            $rule = new Rule([
                'is_or_mode' => $rule['is_or_mode'] ?? false,
            ]);
            $rules[] = $rule;
        }

        $customForm->ownedRules()->saveMany($rules);

        $count = 0;
        foreach ($rawDataRule as $ruleRaw) {
            $this->importRuleElements($rules[$count], $ruleRaw);
            $count++;
        }
    }

    // @codeCoverageIgnoreStart

    public function importRuleElements(Rule $rule, array $rawRuleData): void
    {
        $events = $this->mapRuleElements($rawRuleData['events'] ?? [], RuleEvent::class);
        $triggers = $this->mapRuleElements($rawRuleData['triggers'] ?? [], RuleTrigger::class);

        $rule->ruleEvents()->saveMany($events);
        $rule->ruleTriggers()->saveMany($triggers);
    }

    // @codeCoverageIgnoreEnd

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
