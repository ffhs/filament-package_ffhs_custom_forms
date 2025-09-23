<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $custom_form_id
 * @property string|null $short_title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CustomFieldAnswer> $customFieldAnswers
 * @property-read int|null $custom_field_answers_count
 * @property-read CustomForm $customForm
 * @property-read string $cache_key_for
 * @method static Builder<static>|CustomFormAnswer newModelQuery()
 * @method static Builder<static>|CustomFormAnswer newQuery()
 * @method static Builder<static>|CustomFormAnswer query()
 * @method static Builder<static>|CustomFormAnswer whereCreatedAt($value)
 * @method static Builder<static>|CustomFormAnswer whereCustomFormId($value)
 * @method static Builder<static>|CustomFormAnswer whereId($value)
 * @method static Builder<static>|CustomFormAnswer whereShortTitle($value)
 * @method static Builder<static>|CustomFormAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomFormAnswer extends Model implements EmbedCustomFormAnswer
{
    use CanLoadFormAnswer;

    protected $fillable = [
        'custom_form_id',
        'short_title',
    ];

    public static function __(string ...$args): string
    {
        return CustomForms::__('models.custom_form_answer.' . implode('.', $args));
    }

    public function customForm(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class);
    }

    public function customFieldAnswers(): HasMany
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }

    public function loadedData(): array
    {
        if ($this->relationLoaded('fieldData')) {
            return $this->getRelation('fieldData');
        }

        $fieldData = $this->loadCustomAnswerData($this);
        $this->setRelation('fieldData', $fieldData);

        return $fieldData;
    }

    public function reloadData(): void
    {
        $fieldData = $this->loadCustomAnswerData($this);
        $this->setRelation('fieldData', $fieldData);
    }

    public function getCustomFieldAnswers(): \Illuminate\Support\Collection
    {
        return $this->customFieldAnswers;
    }

    public function getCustomForm(): CustomForm
    {
        return $this->customForm;
    }
}
