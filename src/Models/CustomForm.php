<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $custom_form_identifier
 * @property string|null $short_title
 * @property int|null relation_model_id
 * @property string|null relation_model_type
 * @property Collection $customFormAnswers
 * @property Collection $customFields
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
            if(!is_null($customForm->relation_model_id)) return;
            $customForm->customForm()->save($customForm);
            $customForm->save();
        });

    }


    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class)->orderBy("form_position");
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
    public function variationModelsChached(): Collection {
        return Cache::remember(
            "custom_form-". $this->id . "_variation_models",
            1,
            fn()=>$this->dynamicFormConfiguration()::relationVariationsQuery($this->relationModel())->get()
        );
    }

    //toDo get CustomFieldLayout

    public static function cached(int $id):CustomForm {
        return Cache::remember("custom_form-" .$id, 1, fn()=>CustomForm::query()->firstWhere("id", $id));
    }

    public function customForm(): MorphOne {
        return $this->morphOne(CustomForm::class, "relation_model");
    }

    public function customFormAnsware(): HasMany {
        return $this->hasMany(CustomFormAnswer::class);
    }

}
