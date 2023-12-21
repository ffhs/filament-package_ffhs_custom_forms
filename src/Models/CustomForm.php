<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CustomForm extends Model
{
    use HasFormIdentifyer;
    protected $fillable = [
        'custom_form_identifier',
        'short_title',
    ];

    public function generalFields(): HasManyThrough {
        return $this->hasManyThrough(GeneralField::class,GeneralFieldForm::class,"custom_form_identifier");
    }

    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }




}
