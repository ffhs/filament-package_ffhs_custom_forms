<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $custom_form_id
 * @property int $id
 * @property CustomForm $customForm
 * @property Collection $customFieldAnswers
 * @property string|null $short_title
 */
class CustomFormAnswer extends Model implements CachedModel
{
    use HasCacheModel;


    protected $fillable = [
            'custom_form_id',
            'short_title',
    ];

    protected  array $cachedRelations = [
        "customFieldAnswers",
        "customForm"
    ];

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function cachedCustomFieldAnswers (): mixed {
        return Cache::remember($this->getCacheKeyForAttribute("customFieldAnswers"), self::getCacheDuration(),
            function(){
                $answers = $this->customFieldAnswers()
                    ->with("customField")
                    ->get();
                $this->customForm->customFields;

                return $answers;
               // return new RelationCachedInformations(CustomFieldAnswer::class, $answers->pluck("id")->toArray());
            }
        );
    }

    public function customFieldAnswers (): HasMany {
        return $this->hasMany(CustomFieldAnswer::class);
    }
}
