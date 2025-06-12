<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $order
 * @property string $type
 * @property array<array-key, mixed>|null $data
 * @property int $rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $cache_key_for
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RuleEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RuleEvent extends Model implements CachedModel
{
    use HasCacheModel;


    protected array $cachedRelations = [
        'rule',
    ];

    protected $fillable = [
        'rule_id',
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

    public function getType(): EventType
    {
        return collect(config('ffhs_custom_forms.rule.event'))->firstWhere(fn ($type) =>$type::identifier() == $this->type)::make();
    }
}
