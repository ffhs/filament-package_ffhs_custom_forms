<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 *
 *
 * @property int $id
 * @property int $custom_form_answer_id
 * @property int $custom_field_id
 * @property array<array-key, mixed>|null $answer
 * @property string|null $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField $customField
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm $customForm
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer $customFormAnswer
 * @property-read string $cache_key_for
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereCustomFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereCustomFormAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomFieldAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomFieldAnswer extends Model implements CachedModel
{
    use HasCacheModel;
    use LogsActivity;

    //private array $data = [];

    protected $fillable = [
        'custom_form_answer_id',
        'custom_field_id',
        'answer',
        'path',
    ];


    protected $casts = [
        'answer' => 'array',
    ];


    protected array $cachedRelations = [
        'customForm',
        'customField',
        'customFormAnswer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['answer', 'path']);
    }

    public function customForm(): BelongsTo
    {
        return $this->customFormAnswer->customForm();
    }

    public function cachedCustomForm(): CustomForm
    {
        return $this->customField->customForm;
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
