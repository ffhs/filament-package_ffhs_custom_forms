<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Support\Collection;

trait CanSaveCustomFormEditorRules
{
    protected function savingRules(array $rawRules, CustomForm $form): void
    {
        $usedRuleIds = collect($rawRules)->pluck('id');

        if (!$usedRuleIds->isEmpty()) {
            $form->ownedRules()->whereNotIn('rules.id', $usedRuleIds)->delete();
        }

        $rules = $this->saveRuleComponents($rawRules, $form);

        $form->ownedRules()->sync($rules->pluck('id'));
    }

    private function saveRuleComponents(array $rawRules, CustomForm $form): Collection
    {
        $existingRules = $form->ownedRules->keyBy('id');
        $rules = collect();

        $ruleEventsToDelete = [];
        $ruleEventsToCreate = [];
        $ruleEventsToUpdate = [];

        $ruleTriggersToDelete = [];
        $ruleTriggersToCreate = [];
        $ruleTriggersToUpdate = [];

        $existingTriggers = RuleTrigger::query()
            ->whereIn('rule_id', $form->ownedRules()->select('rules.id'))
            ->get()
            ->groupBy('rule_id');

        $existingEvents = RuleEvent::query()
            ->whereIn('rule_id', $form->ownedRules()->select('rules.id'))
            ->get()
            ->groupBy('rule_id');

        foreach ($rawRules as $rawRule) {
            $rule = $this->resolveRule($rawRule, $existingRules, Rule::class);
            $rule->is_or_mode = $rawRule['is_or_mode'] ?? false;
            $rule->save();

            $triggers = $existingTriggers[$rule->id] ?? collect();
            $triggers = $triggers->keyBy('id');
            $rawTriggers = $rawRule['triggers'] ?? [];
            $usedTriggerIds = collect($rawTriggers)->pluck('id')->toArray();

            $events = $existingEvents[$rule->id] ?? collect();
            $events = $events->keyBy('id');
            $rawEvents = $rawRule['events'] ?? [];
            $usedEventIds = collect($rawEvents)->pluck('id')->toArray();

            $ruleTriggersToDelete = [
                ...$ruleTriggersToDelete,
                ...$triggers
                    ->whereNotIn('id', $usedTriggerIds)
                    ->pluck('id')
            ];
            $ruleEventsToDelete = [
                ...$ruleEventsToDelete,
                ...$events
                    ->whereNotIn('id', $usedEventIds)
                    ->pluck('id')
            ];

            [$updatedTriggers, $createdTriggers] = $this->updateRuleComponent($rawTriggers, $triggers, $rule,
                app(RuleTrigger::class));
            $ruleTriggersToCreate = [
                ...$ruleTriggersToCreate,
                ...$createdTriggers
            ];
            $ruleTriggersToUpdate = [
                ...$ruleTriggersToUpdate,
                ...$updatedTriggers
            ];

            [$updatedEvents, $createdEvents] = $this->updateRuleComponent($rawEvents, $events, $rule,
                app(RuleTrigger::class));
            $ruleEventsToCreate = [
                ...$ruleEventsToCreate,
                ...$createdEvents
            ];
            $ruleEventsToUpdate = [
                ...$ruleEventsToUpdate,
                ...$updatedEvents
            ];


            $rule->cachedClear('ruleTriggers');
            $rule->cachedClear('ruleEvents');
            $rules->add($rule);
        }

        RuleTrigger::query()->whereIn('id', $ruleTriggersToDelete)->delete();
        RuleEvent::query()->whereIn('id', $ruleEventsToDelete)->delete();

        RuleTrigger::insert($ruleTriggersToCreate);
        RuleEvent::insert($ruleEventsToUpdate);
        RuleTrigger::upsert($ruleTriggersToUpdate, ['id']);
        RuleEvent::upsert($ruleEventsToUpdate, ['id']);

        return $rules;
    }

    private function resolveRule(
        array $rawData,
        Collection $existingComponents,
        string $type
    ): RuleTrigger|RuleEvent|Rule {
        if (empty($rawData['id'])) {
            return app($type);
        }
        
        return $existingComponents->get($rawData['id']) ?? app($type);
    }

    private function updateRuleComponent(
        mixed $rawComponents,
        Collection $existingComponents,
        Rule $rule,
        RuleTrigger|RuleEvent $type
    ): array {
        $updatedComponent = [];
        $createdComponents = [];

        foreach ($rawComponents as $rawComponent) {

            /**@var RuleTrigger|RuleEvent $ruleComponent */
            $ruleComponent = $this->resolveRule($rawComponent, $existingComponents, $type::class);


            $ruleComponent->fill($rawComponent);
            $ruleComponent->rule_id = $rule->id;

            if ($ruleComponent->exists) {
                if ($ruleComponent->isDirty()) {
                    $updatedComponent[] = [
                        ...$ruleComponent->getAttributes(),
                        'updated_at' => now()
                    ];
                }
            } else {
                $createdComponents[] = [
                    ...$ruleComponent->getAttributes(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

        }

        return [$updatedComponent, $createdComponents];
    }
}
