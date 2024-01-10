<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $custom_form_id
 * @property CustomForm $customForm
 * @property String $short_title
 * @property bool $is_disabled
 *
 */

class FormVariation extends Model
{
    use HasFormIdentifyer;
    protected $fillable = [
        'custom_form_id',
        'short_title',
        'is_disabled',
    ];

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    //toDo get CustomFieldVariations


}
