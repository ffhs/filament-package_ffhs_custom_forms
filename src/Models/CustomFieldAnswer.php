<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $id
 * @property int $custom_form_answer_id
 * @property int $custom_field_id
 * @property CustomFormAnswer $customFormAnswer
 * @property CustomForm $customForm
 * @property CustomField $customField
 * @property array $answer
 */
class CustomFieldAnswer extends Model implements CachedModel
{
    use HasCacheModel;

    //private array $data = [];

    protected $fillable = [
        'custom_form_answer_id',
        'custom_field_id',
        'answer'
    ];


    protected $casts = [
        'answer'=>'array',
    ];


    protected array $cachedRelations = [
        'customForm',
        "customField",
        "customFormAnswer",
    ];


    public function customForm(): HasOneThrough
    {
        return $this->hasOneThrough(CustomForm::class,CustomField::class);
    }

    public function cachedCustomForm(): CustomForm {
        return $this->customField->customForm;
    }

    public function customField (): BelongsTo {
        return $this->belongsTo(CustomField::class);
    }

    public function customFormAnswer (): BelongsTo {
        return $this->belongsTo(CustomFormAnswer::class);
    }

}
