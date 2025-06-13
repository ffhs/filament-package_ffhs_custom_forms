<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $custom_form_id
 * @property int $rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm $customForm
 * @property-read string $cache_key_for
 * @property-read Rule $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule whereCustomFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormRule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FormRule extends Model
{

    protected $fillable = [
        'order',
        'custom_form_id',
        'rule_id',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function customForm(): belongsTo
    {
        return $this->belongsTo(CustomForm::class);
    }
}
