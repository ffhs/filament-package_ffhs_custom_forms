<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $custom_form_identifier
 */
class CustomForm extends Model
{
    use HasFormIdentifyer;
    protected $fillable = [
        'custom_form_identifier',
        'short_title',
        'relation_model_id',
        'relation_model_type',
    ];



    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }


    public function relationModel(): \Illuminate\Database\Eloquent\Relations\MorphTo {
      return $this->morphTo();
    }



    public function relatedModels() {
        if(!$this->dynamicFormConfiguration()::hasVariations)
            return null;
        else if($this->dynamicFormConfiguration()::hasRelationVariations)
            return $this->dynamicFormConfiguration()::relationVariationsQuery($this->relationModel());
        else
            return $this->hasMany(FormVariation::class);
    }


    //toDo get CustomFieldLayout

    public static function cached(int $id):CustomForm {
        return Cache::remember("custom_form-" .$id, 1, fn()=>CustomForm::query()->firstWhere("id", $id));
    }

}
