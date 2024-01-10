<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $custom_form_identifier
 * @property string|null $short_title
 * @property int|null relation_model_id
 * @property string|null relation_model_type
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

    protected static function booted() {
        parent::booted();

        self::created(function(CustomForm $customForm) {
            if(!$customForm->getFormConfiguration()::hasVariations()) return;
            if(!is_null("relation_model_id")) return;
            $customForm->customForm()->save($customForm);
        });

    }


    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }


    public function relationModel(): MorphTo {
      return $this->morphTo();
    }


    public function getFormConfiguration():string{
        return DynamicFormConfiguration::getFormConfigurationClass($this->custom_form_identifier);
    }



    public function variationModels(): Builder {
        return $this->dynamicFormConfiguration()::relationVariationsQuery($this->relationModel());
    }


    //toDo get CustomFieldLayout

    public static function cached(int $id):CustomForm {
        return Cache::remember("custom_form-" .$id, 1, fn()=>CustomForm::query()->firstWhere("id", $id));
    }

    public function customForm(): MorphOne {
        return $this->morphOne(CustomForm::class, "relation_model");
    }

}
