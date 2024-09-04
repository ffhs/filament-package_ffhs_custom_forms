<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormLoadHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $custom_form_id
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
    protected  array $cachedBelongsTo = [
        "customForm" => ["custom_form_id", "id"],
    ];
    protected  array $cachedRelations = [
        "customFieldAnswers",
    ];

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function customFieldAnswers (): HasMany {
        return $this->hasMany(CustomFieldAnswer::class);
    }
    public function cachedCustomFieldAnswers (): Collection {
        return $this->cachedAnswers();
    }

    public function cachedAnswers():Collection {
        return Cache::remember("answers-from-custom_form_answers_" . $this->id,config('ffhs_custom_forms.cache_duration'),
            function(){
                $answers = $this->customFieldAnswers()->get();
                $this->customForm->customFields;
                return $answers;
            }
        );
    }



    public function cachedLoadedAnswares()
    {
        return Cache::remember($this->getCacheKeyForAttribute("cachedLoadedAnswares"), 1 , fn() => CustomFormLoadHelper::load($this));
    }
}
