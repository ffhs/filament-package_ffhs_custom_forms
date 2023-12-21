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
        'relation_model',
    ];

    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }


    public function relationModel() {
       $this->morphTo();
    }


    //toDo get CustomFieldLayout


}
