<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $identify_key
 * @property bool $is_general_field_active
 * @property bool $is_term_bound
 * @property null|string $tool_tip_de
 * @property null|string $tool_tip_en
 * @property string $name_de
 * @property string $name_en
 * @property string $type
 * @property string $icon
 * @property array $extra_options
 * @property Collection $customOptions
 * @property Collection $generalFieldForms
 */
class GeneralField extends ACustomField
{

    protected $table = "general_fields";

    protected $fillable = [
        'variation_options',
        'is_term_bound',
        'extra_options',
        'identify_key',
        'tool_tip_de',
        'tool_tip_en',
        'is_active',
        'name_de',
        'name_en',
        'type',
        'icon',
    ];

    protected $casts = [
        'extra_options'=>'array',
    ];

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }


    public function generalFieldForms(): HasMany {
        return $this->hasMany(GeneralFieldForm::class);
    }

    public function customOptions(): BelongsToMany {
        return $this->belongsToMany(CustomOption::class, "option_general_field");
    }




}
