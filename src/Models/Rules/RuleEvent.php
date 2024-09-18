<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Rules;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array $data
 * @property string $type
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
        return collect(config("ffhs_custom_forms.rule.event"))->firstWhere(fn ($type) =>$type::identifier() == $this->type)::make();
    }
}
