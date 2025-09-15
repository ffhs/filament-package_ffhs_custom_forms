<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 *
 *
 * @property int $id
 * @property int $custom_form_answer_id
 * @property int $custom_field_id
 * @property array<array-key, mixed>|null $answer
 * @property string|null $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read CustomField $customField
 * @property-read CustomForm $customForm
 * @property-read CustomFormAnswer $customFormAnswer
 * @property-read string $cache_key_for
 * @method static Builder<static>|CustomFieldAnswer newModelQuery()
 * @method static Builder<static>|CustomFieldAnswer newQuery()
 * @method static Builder<static>|CustomFieldAnswer query()
 * @method static Builder<static>|CustomFieldAnswer whereAnswer($value)
 * @method static Builder<static>|CustomFieldAnswer whereCreatedAt($value)
 * @method static Builder<static>|CustomFieldAnswer whereCustomFieldId($value)
 * @method static Builder<static>|CustomFieldAnswer whereCustomFormAnswerId($value)
 * @method static Builder<static>|CustomFieldAnswer whereId($value)
 * @method static Builder<static>|CustomFieldAnswer wherePath($value)
 * @method static Builder<static>|CustomFieldAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomFieldAnswer extends Model implements EmbedCustomFieldAnswer
{
    use LogsActivity;

    protected $fillable = [
        'custom_form_answer_id',
        'custom_field_id',
        'answer',
        'path',
    ];


    protected $casts = [
        'answer' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['answer', 'path']);
    }

    public function customForm(): BelongsTo
    {
        return $this
            ->customFormAnswer
            ->customForm();
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function customFormAnswer(): BelongsTo
    {
        return $this->belongsTo(CustomFormAnswer::class);
    }
}
