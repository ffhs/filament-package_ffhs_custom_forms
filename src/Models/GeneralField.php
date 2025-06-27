<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFormModelTranslations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int|null $is_active
 * @property string $icon
 * @property string|null $identifier
 * @property array<array-key, mixed>|null $overwrite_options
 * @property array<array-key, mixed>|null $options
 * @property array<array-key, mixed>|null $name
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField> $customFields
 * @property-read int|null $custom_fields_count
 * @property-read Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption> $customOptions
 * @property-read int|null $custom_options_count
 * @property-read Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm> $generalFieldForms
 * @property-read int|null $general_field_forms_count
 * @property-read string $cache_key_for
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereOverwriteOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GeneralField extends ACustomField
{
    use HasCustomFormModelTranslations;

    protected static string $translationName = 'general_field';

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
