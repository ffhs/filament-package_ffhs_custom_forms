<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $custom_form_id
 * @property CustomForm $customForm
 * @property Collection $customFieldAnswers
 * @property int $id
 * @property string|null $short_title
 */
class CustomFormAnswer extends Model
{
    protected $fillable = [
            'custom_form_id',
            'short_title'
        ];

    public function customForm (): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function customFieldAnswers (): HasMany {
        return $this->hasMany(CustomFieldAnswer::class);
    }
}
