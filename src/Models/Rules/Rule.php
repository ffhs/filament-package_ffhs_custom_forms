<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
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

    protected array $cachedManyRelations = [
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
        $triggered = is_null($this->ruleTriggers->sortBy("order")->firstWhere(function(RuleTrigger $trigger) use ($arguments, $target) {
            $triggered = $trigger->getType()->isTrigger($arguments, $target, $trigger);
            if($triggered || $trigger->is_inverted) return false;
        }));


        $this->ruleEvents->sortBy("order")->firstWhere(function(RuleEvent $eve) use ($triggered, $arguments, &$target) {
            $target = $eve->getType()->handle($triggered,$arguments, $target, $eve);
        });

        return $target;
    }
}
