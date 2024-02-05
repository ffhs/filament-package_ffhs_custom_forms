<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property string $identify_key
 * @property bool $is_general_field_active
 * @property bool $is_term_bound
 * @property null|string $tool_tip_de
 * @property null|string $tool_tip_en
 * @property string $name_de
 * @property string $name_en
 * @property string $type
 * @property Collection $customOptions
 */
class GeneralField extends ACustomField
{

    protected $table = "general_fields";

    protected $fillable = [
        'identify_key',
        'is_active',
        'is_term_bound',
        'tool_tip_de',
        'tool_tip_en',
        'name_de',
        'name_en',
        'type',
    ];


    public static function getTranslatableAttributes(): array
    {
        return ['name','tool_tip'];
    }


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


    public static function cached(mixed $custom_field_id): ?ACustomField{
        return Cache::remember("custom_field-" .$custom_field_id, 1, fn()=>GeneralField::query()->firstWhere("id", $custom_field_id));
    }

    public static function allCached(): Collection{
        return Cache::remember("general_fields-all", 5,fn()=>self::all());
    }


}
