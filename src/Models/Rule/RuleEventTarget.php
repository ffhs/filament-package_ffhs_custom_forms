<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RuleEventTarget extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations =[
        'ruleEvent' => ['rule_event_id' => 'id'],
        'target' => ['dependence_id' => 'id']
    ];

    protected $fillable = [
        'rule_event_id',
        'dependence_id',
        'dependence_model',
    ];

    public function ruleEvent(): BelongsTo
    {
        return $this->belongsTo(RuleEvent::class);
    }

    public function dependence(): MorphTo
    {
        return $this->morphTo();
    }

}
