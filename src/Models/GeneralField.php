<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class GeneralField extends ACustomField
{


    protected $fillable = [
        'identify_key',
        'is_general_field_active',
        'is_general_field',
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



    //None no generalFields
    protected static function booted()
    {
        static::addGlobalScope('is_general_field', function (Builder $builder) {
            $builder->where('is_general_field', true);
        });

        static::creating(function (GeneralField $field) {
            $field->is_general_field = true;
        });
    }


    public static function allCached(): Collection{
       return Cache::remember("general_fields-all", 5,fn()=>self::all());
    }

    public function customFields(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    // Relation for dropdownOptions
    public function dropdownOptions(): BelongsToMany
    {
        return $this->belongsToMany(DropdownOption::class);
    }
    public static function cached(mixed $custom_field_id): ?ACustomField{
        return Cache::remember("custom_field-" .$custom_field_id, 1, fn()=>GeneralField::query()->firstWhere("id", $custom_field_id));
    }


}
