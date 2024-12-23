<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CustomForm $customForm
 * @property Rule $rule
 * @property int order
 */
class FormRule extends Model implements CachedModel
{
    use HasCacheModel;

    protected array $cachedRelations = [
        "rule",
        "customForm",
    ];

    protected $fillable = [
        "order",
        "custom_form_id",
        "rule_id",
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
