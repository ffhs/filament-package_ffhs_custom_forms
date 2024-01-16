<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    use HasFactory;



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


    public function customFieldInLayout(): HasMany {


        $subQuery = CustomField::query()
            ->where("custom_form_id", $this->id)
            ->whereIn("type", collect(config("ffhs_custom_forms.custom_field_types"))
                ->filter(fn(string $type) => (new $type()) instanceof CustomLayoutType)
                ->map(fn(string $type) => $type::getFieldIdentifier())
            );

        return $this->hasMany(CustomField::class)
            ->whereNotIn("id",
                CustomField::query()
                    ->select("id")
                    ->where("form_position", ">",$subQuery->clone()
                        ->select("form_position")
                        ->orderBy("form_position", "ASC")
                        ->limit(1))
                    ->where("form_position", "<=",$subQuery->clone()
                        ->select("layout_end_position")
                        ->orderBy("layout_end_position", "DESC")
                        ->limit(1))
            )
            ->orderBy("form_position");
    }



}
