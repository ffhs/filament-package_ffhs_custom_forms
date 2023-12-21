<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
class FormVariations extends Model
{
    use HasFormIdentifyer;
    protected $fillable = [
        'custom_form_id',
        'short_title',
    ];

    public function customForm(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    //toDo get CustomFieldVariations


}
