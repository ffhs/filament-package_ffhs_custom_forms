<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $custom_form_id
 * @property CustomForm $customForm
 * @property Collection $customFieldAnswers
 * @property int $id
 * @property string|null $short_title
 */
class CustomFormAnswer extends CachedModel
{
    protected $fillable = [
            'custom_form_id',
            'short_title',
        ];
    protected  array $cachedRelations = [
        "customForm" => ["custom_form_id", "id"],
    ];

    public function customForm (): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function customFieldAnswers (): HasMany {
        return $this->hasMany(CustomFieldAnswer::class);
    }

    public function cachedAnswers():Collection {
        return Cache::remember("answers-from-custom_form_answers_" . $this->id,config('ffhs_custom_forms.cache_duration'),
            function(){
                $answers = $this->customFieldAnswers()->get();
                $this->customForm->cachedFieldsWithTemplates();
                return $answers;
            }
        );
    }
}
