<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * 
 *
 * @property int $id
 * @property int $is_or_mode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $cache_key_for
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent> $ruleEvents
 * @property-read int|null $rule_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger> $ruleTriggers
 * @property-read int|null $rule_triggers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereIsOrMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rule extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations = [
        "ruleTriggers",
        "ruleEvents",
    ];

    protected $fillable = [
        "is_or_mode",
    ];

    public function handle(array $arguments, mixed $target): mixed
    {
        $triggers = $this->getTriggersCallback($target, $arguments);

        $events = $this->ruleEvents;
        if($events == null) $events = $this->ruleEvents()->get();
        $events = $events->sortBy("order");
        foreach ($events as $event) {
            /**@var RuleEvent $event*/
            $targetResult = $event->getType()->handle($triggers,$arguments, $target, $event);
            if(!is_null($targetResult)) $target = $targetResult;
        }
        return $target;
    }

    public function getTriggersCallback(mixed $target, array $arguments): Closure {
        return function ($extraArguments = []) use ($target, $arguments) {
            $argumentsFinal = array_merge($arguments, $extraArguments);

            $triggers = $this->ruleTriggers;
            if($triggers == null) $triggers = $this->ruleTriggers()->get();
            $triggers = $triggers->sortBy("order");

            foreach ($triggers as $trigger) {
                /**@var RuleTrigger $trigger */
                $triggered = $trigger->getType()->isTrigger($argumentsFinal, $target, $trigger);
                if($trigger->is_inverted) $triggered = !$triggered;

                if ($this->is_or_mode && $triggered) return true; //OR
                else if (!$this->is_or_mode && !$triggered) return false; //AND
            }

            return !$this->is_or_mode;
        };
    }

    public function ruleTriggers(): HasMany
    {
        return $this->hasMany(RuleTrigger::class);
    }

    public function ruleEvents(): HasMany
    {
        return $this->hasMany(RuleEvent::class);
    }
}
