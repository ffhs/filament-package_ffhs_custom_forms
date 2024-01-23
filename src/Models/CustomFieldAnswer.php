<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $custom_form_answer_id
 * @property int $custom_field_variation_id
 * @property CustomFieldVariation $customFieldVariation
 * @property array answer
 * @property CustomFormAnswer $customFormAnswer
 */
class CustomFieldAnswer extends Model
{
    protected $fillable = [
        'custom_form_answer_id',
        'custom_field_variation_id',
        'answer'
    ];


    protected $casts = [
        'answer'=>'array',
    ];

    public function customFieldVariation () {
        return $this->belongsTo(CustomFieldVariation::class);
    }

    public function customFormAnswer () {
        return $this->belongsTo(CustomFormAnswer::class);
    }

}
