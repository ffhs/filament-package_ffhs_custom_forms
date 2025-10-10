<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFormModelTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 *
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $identifier
 * @property string|null $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $cache_key_for
 * @property-read mixed $translations
 * @method static Builder<static>|CustomOption newModelQuery()
 * @method static Builder<static>|CustomOption newQuery()
 * @method static Builder<static>|CustomOption query()
 * @method static Builder<static>|CustomOption whereCreatedAt($value)
 * @method static Builder<static>|CustomOption whereIcon($value)
 * @method static Builder<static>|CustomOption whereId($value)
 * @method static Builder<static>|CustomOption whereIdentifier($value)
 * @method static Builder<static>|CustomOption whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomOption whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomOption whereLocale(string $column, string $locale)
 * @method static Builder<static>|CustomOption whereLocales(string $column, array $locales)
 * @method static Builder<static>|CustomOption whereName($value)
 * @method static Builder<static>|CustomOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomOption extends Model
{
    use HasTranslations;
    use HasFactory;
    use HasCustomFormModelTranslations;

    protected static string $translationName = 'custom_option';
    public array $translatable = ['name'];
    protected $fillable = [
        'name',
        'identifier',
        'icon',
    ];

    protected static function booted(): void
    {
        parent::booted();
        self::creating(function (CustomOption $customOption) {
            /**@phpstan-ignore-next-line */
            if (is_null($customOption->identifier)) {
                $customOption->identifier = uniqid();
            }
        });
    }
}
