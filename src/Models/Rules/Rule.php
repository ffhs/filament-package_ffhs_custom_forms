<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property bool is_or_mode
 * @property Collection ruleEvents
 * @property Collection ruleTriggers
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

    public function ruleTriggers(): HasMany
    {
        return $this->hasMany(RuleTrigger::class);
    }

    public function ruleEvents(): HasMany
    {
        return $this->hasMany(RuleEvent::class);
    }

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


    public function getTriggersCallback(mixed $target, array $arguments): \Closure
    {
        return function ($extraArguments = []) use ($target, $arguments) {
            $argumentsFinal = array_merge($arguments, $extraArguments);

            $triggers = $this->ruleTriggers;
            if($triggers == null) $triggers = $this->ruleTriggers()->get();
            $triggers = $triggers->sortBy("order");

            foreach ($triggers as $trigger) {
                /**@var RuleTrigger $trigger */
                $triggered = $trigger->getType()->isTrigger($argumentsFinal, $target, $trigger);
                $triggered = $triggered != $trigger->is_inverted;

                if ($this->is_or_mode && $triggered) return true;//OR
                else if (!$this->is_or_mode && !$triggered) return false; //AND
            }

            return !$this->is_or_mode;
        };
    }
}
