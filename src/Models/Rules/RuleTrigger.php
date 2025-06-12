<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\TriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $is_inverted
 * @property int $order
 * @property string $type
 * @property array<array-key, mixed>|null $data
 * @property int $rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $cache_key_for
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereIsInverted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleTrigger whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RuleTrigger extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations = [
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
        return collect(config('ffhs_custom_forms.rule.trigger'))->firstWhere(
            fn($type) => $type::identifier() == $this->type
        )::make();
    }

}
