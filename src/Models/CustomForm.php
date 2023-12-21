<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomForm extends Model
{
    use HasFormIdentifyer;
    protected $fillable = [
        'custom_form_identifier',
        'short_title',
    ];

    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }

    //toDo get CustomFieldLayout


}
