<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int rule_id
 * @property int order
 * @property string type
 * @property array data
 * @property boolean is_inverted
 *
 *
 */
class RuleTrigger extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations =[
        'rule',
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

    public function getType(): TriggerType
    {
        return collect(config("ffhs_custom_forms.rule.trigger"))->firstWhere(fn ($type) =>$type::identifier() == $this->type)::make();
    }

}
