<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 *
 * @property bool $required
 * @property bool is_active
 * @property array options
 *
 * @property CustomField customField
 * @property mixed|null $variation
 *
 * @property int|null $variation_id
 * @property string|null $variation_type
 * @property int $custom_field_id
 */
class CustomFieldVariation extends Model
{
    use HasFactory;

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
        'active' => 'boolean',
        'options'=>'array'
    ];


    public function isTemplate(): bool {
        return is_null($this->variation_id);
    }

    public function variation(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

    public function customField(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }


/*
    ///Relation for dropdownOptions
    public function dropdownOptions(): BelongsToMany
    {
        return $this->belongsToMany(DropdownOption::class);

     */
}
