<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property bool is_or_mode
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

}
