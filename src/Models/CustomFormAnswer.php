<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int $custom_form_id
 * @property string|null $short_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer> $customFieldAnswers
 * @property-read int|null $custom_field_answers_count
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm $customForm
 * @property-read string $cache_key_for
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer whereCustomFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer whereShortTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFormAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomFormAnswer extends Model implements CachedModel
{
    use HasCacheModel;


    protected $fillable = [
        'custom_form_id',
        'short_title',
    ];

//    protected array $cachedRelations = [
//        'customFieldAnswers',
//        'customForm'
//    ];

    public static function __(string ...$args): string
    {
        return CustomForms::__('models.custom_form_answer.' . implode('.', $args));
    }

    public function customForm(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class);
    }

//    public function cachedCustomFieldAnswers(): mixed
//    {
//        return Cache::remember($this->getCacheKeyForAttribute('customFieldAnswers'), self::getCacheDuration(),
//            function () {
//                $answers = $this->customFieldAnswers()
//                    ->with('customField')
//                    ->get();
//                $this->customForm->customFields;
//
//                return $answers;
//                // return new RelationCachedInformations(CustomFieldAnswer::class, $answers->pluck('id')->toArray());
//            }
//        );
//    }

    public function customFieldAnswers(): HasMany
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }
}
