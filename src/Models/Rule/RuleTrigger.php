<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int rule_id
 * @property int order
 * @property string type
 * @property array data
 */
class RuleTrigger extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations =[
        'rule' => ['rule_id' => 'id']
    ];

    protected $fillable = [
        'rule_id',
        'is_inverted',
        'order',
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];


    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

}
