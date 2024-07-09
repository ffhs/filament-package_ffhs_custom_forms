<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RuleTriggerDependence extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations =[
        'ruleTrigger' => ['rule_trigger_id' => 'id'],
        'dependence' => ['dependence_id' => 'id']
    ];

    protected $fillable = [
        'order',
        'rule_trigger_id',
        'dependence_id',
        'dependence_model',
    ];


    public function ruleTrigger(): BelongsTo
    {
        return $this->belongsTo(RuleTrigger::class);
    }

    public function dependence(): MorphTo
    {
        return $this->morphTo();
    }


}
