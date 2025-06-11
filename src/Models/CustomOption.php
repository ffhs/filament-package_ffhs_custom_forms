<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFormModelTranslations;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 *
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $identifier
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $cache_key_for
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomOption extends Model implements CachedModel
{
    use HasFormIdentifier;
    use HasTranslations;
    use HasCacheModel;
    use HasFactory;
    use HasCustomFormModelTranslations;

    protected static string $translationName = 'custom_option';
    public $translatable = ['name'];
    protected $fillable = [
        'name',
        'identifier',
        'icon',
    ];


    protected static function booted()
    {
        parent::booted();
        self::creating(function (CustomOption $customOption) {
            if (is_null($customOption->identifier)) {
                $customOption->identifier = uniqid();
            }
        });
    }


}
