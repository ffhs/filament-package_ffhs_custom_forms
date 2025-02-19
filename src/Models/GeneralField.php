<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $identifier
 * @property string $type
 * @property string $icon
 * @property array $overwrite_options
 * @property array $options
 * @property Collection $customOptions
 * @property Collection $generalFieldForms
 */
class GeneralField extends ACustomField
{
    protected $table = 'general_fields';

    protected $fillable = [
//        'is_term_bound',
        'overwrite_options',
        'options',
        'identifier',
        'is_active',
        'name',
        'type',
        'icon',
    ];

    protected $casts = [
        'extra_options' => 'array',
        'overwrite_options' => 'array',
        'options' => 'array',
    ];

    protected array $cachedRelations = [
        'customFields',
        'generalFieldForms',
        'customOptions',
    ];

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function generalFieldForms(): HasMany
    {
        return $this->hasMany(GeneralFieldForm::class);
    }

    public function customOptions(): BelongsToMany
    {
        return $this->belongsToMany(CustomOption::class, 'option_general_field');
    }
}
