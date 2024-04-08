<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $custom_form_identifier
 * @property string|null $short_title
 * @property Collection $customFormAnswers
 * @property Collection $customFields
 * @property Collection $generalFields
 * @property bool $is_template
 */
class CustomForm extends Model
{
    use HasFormIdentifier;
    use HasFactory;


    protected $fillable = [
        'custom_form_identifier',
        'short_title',
        'is_template',
    ];


    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class)->orderBy("form_position"); //ToDo also add Templates field
    }

    public function customFieldsWithTemplateFields(): Builder {
        $baseQuery = CustomField::query()->where("custom_form_id",$this->id);
        $templateQuery = $baseQuery->clone()->select("template_id")->whereNotNull("template_id");
        return $baseQuery->orWhereIn("custom_form_id",$templateQuery)->orderBy("form_position"); //$this->hasMany(CustomField::class)->orderBy("form_position"); //ToDo also add Templates field
    }


    public function getFormConfiguration():DynamicFormConfiguration{
        return new (DynamicFormConfiguration::getFormConfigurationClass($this->custom_form_identifier))();
    }



    public static function cached(int $id):CustomForm {
        return Cache::remember("custom_form-" .$id, config('ffhs_custom_forms.cache_duration'), fn()=>CustomForm::query()->firstWhere("id", $id));
    }

    public function makeCached():void {
         Cache::put("custom_form-" .$this->id, $this,config('ffhs_custom_forms.cache_duration'));
    }

    public function customFormAnswers(): HasMany {
        return $this->hasMany(CustomFormAnswer::class);
    }

    public function generalFields(): BelongsToMany {
        return $this->belongsToMany(GeneralField::class, "custom_fields","custom_form_id","general_field_id");
    }


    public function customFieldInLayout(): HasMany {

        $subQueryAlLayouts = CustomField::query()
            ->select('form_position','layout_end_position')
            ->where("custom_form_id", $this->id)
            ->where("layout_end_position","!=", null);


        $query = $this->hasMany(CustomField::class)
            ->whereNotIn("id",
                $this->hasMany(CustomField::class)
                    ->select("id")
                    ->rightJoinSub($subQueryAlLayouts, 'sub', function ($join) {
                        $join->on('custom_fields.form_position', '>', 'sub.form_position')
                        ->on('custom_fields.form_position', '<=', 'sub.layout_end_position');
                    })
            );

        return $query;
    }

    public static function getTemplatesForFormType (string|DynamicFormConfiguration $formType):Collection {
        if($formType instanceof DynamicFormConfiguration)  $formType = $formType::identifier();

        $query=  CustomForm::query()
            ->where("custom_form_identifier", $formType)
            ->where("is_template",true)
            ->with([
                "customFields",
                "customFields.generalField",
                "customFields.customOptions",
                "customFields.generalField.customOptions"
            ]);

        $key ="form_templates_" . $formType;
        $duration = config('ffhs_custom_forms.cache_duration');
        return  Cache::remember($key,$duration, fn() => $query->get());
    }

    public function cachedFields(): Collection {
        return Cache::remember("custom_fields-from-form_" . $this->id,config('ffhs_custom_forms.cache_duration'), fn() => $this->customFields()->with([
            "generalField.customOptions",
        ])->get());
    }

    public function cachedField(int $customFieldId): CustomField|null {
        return $this->cachedFields()->firstWhere("id",$customFieldId);
    }




}
