<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 *
 * @property bool $required
 * @property bool is_active
 * @property array options
 *
 * @property CustomField customField
 * @property string identify_key
 * @property mixed|null $variation
 *
 * @property int|null $variation_id
 * @property string|null $variation_type
 * @property int $custom_field_id
 *
 * @property Collection $customOptions
 */
class CustomFieldVariation extends Model
{

    protected $table = 'custom_field_variation';

    protected $fillable = [
        'required',
        'is_active',
        'options',
        "custom_field_id",
        'variation_id',
        'variation_type',
    ];


    protected $casts = [
        'required' => 'boolean',
        'is_active' => 'boolean',
        'options'=>'array'
    ];


    public function isTemplate(): bool {
        return is_null($this->variation_id);
    }

    public function variation(): MorphTo {
        return $this->morphTo();
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function customOptions(): BelongsToMany {
        return $this->belongsToMany(CustomOption::class, "option_field_variation");
    }
}
