<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int|null general_field_id
 * @property Collection $customFieldVariation
 * @property int  custom_form_id
 * @property bool $has_variations
 * @property int $form_position
 * @property int|null $layout_end_position
 *
 * @property Collection|null customFieldVariations
 * @property string|null identify_key
 *
 * @property CustomForm customForm
 * @property GeneralField|null $generalField
*/

class CustomField extends ACustomField
{
    use HasFactory;

    protected $fillable = [
        'general_field_id',

        'tool_tip_de',
        'tool_tip_en',
        'name_de',
        'name_en',
        'type',

        'custom_form_id',
        'has_variations',
        'form_position',
        'layout_end_position',
        'identify_key',
    ];


    protected static function booted(): void {
        //Only CustomFields and CustomFields where inherit from GeneralFields
        static::addGlobalScope('is_general_field', function (Builder $builder) {
            $builder->where('is_general_field', false);
        });
    }


    public static function inheritCustomField(): Builder {
        return self::query()->whereNot("general_field_id");
    }
    public static function notInheritCustomField(): Builder {
        return self::query()->whereNull("general_field_id");
    }

    public function customFieldVariations (): HasMany {
        return $this->hasMany(CustomFieldVariation::class);
    }
    public function templateVariation ():CustomFieldVariation|null {
        return $this->customFieldVariations->filter(fn($customFieldVariation)=> $customFieldVariation->isTemplate())->first();
    }

    private function getInheritStateFromArrays($thisValues, $generalFieldArray){
        if(is_null($generalFieldArray)) return $thisValues;
        $output= array_replace($thisValues, array_filter($generalFieldArray, fn($value) => !is_null($value)));
        $output["is_general_field"] = false;
        unset($output["id"]);
        unset($output["general_field_id"]);
        unset($output["created_at"]);
        unset($output["updated_at"]);
        return $output;
    }

    /**
     * @return array there are the stat from this Field and the Stats from the GeneralField
     */
    public function getInheritState():array{
        $generalFiledArray = $this->isGeneralField()?$this->generalField()->first()->toArray():null;
        return $this->getInheritStateFromArrays($this->toArray(), $generalFiledArray);
    }

    /**
     * @return array there are the stat from this Field and the Stats from the GeneralField
     */
    public function getInheritStatsFromOrigin() {
        $generalFieldId = $this->getOriginal("general_field_id");
        $generalFieldArray = is_null($generalFieldId)? null: GeneralField::cached($generalFieldId)->toArray();
        return $this->getInheritStateFromArrays($this->getOriginal(), $generalFieldArray);
    }

    public function isGeneralField():bool{
        return !is_null($this->general_field_id);
    }


    public function isInheritFromGeneralField():bool{
        return !is_null($this->general_field_id);
    }

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function generalField(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
    }


    public function getVariation(Model|int $relatedObject ): CustomFieldVariation|null{
        if(!$this->has_variations) return $this->templateVariation();
        if($relatedObject instanceof  Model) $relatedObject = $relatedObject->id;
        $variation =  $this->customFieldVariations->filter(fn(CustomFieldVariation $fieldVariation)=>$fieldVariation->variation_id == $relatedObject);
        if(is_null($variation)) return $this->templateVariation();
        else return $relatedObject;
    }


    public function customFieldVariation(): HasMany {
        return $this->hasMany(CustomFieldVariation::class);
    }

    public static function cached(mixed $custom_field_id): ?CustomField{
        return Cache::remember("custom_field-" .$custom_field_id, 1, fn()=>CustomField::query()->firstWhere("id", $custom_field_id));
    }

    public static function cachedAllInForm(int $formId): Collection{
        return Cache::remember("custom_field-form_id" .$formId, 1, fn()=>CustomField::query()->where("custom_form_id", $formId)->get());
    }


    public function customFieldInLayout(): HasMany {

        if(!($this->getType() instanceof CustomLayoutType))
            return $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                ->where("id",null);


        $subQueryAlLayouts =
            $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
            ->select('form_position','layout_end_position')
            ->where("form_position",">", $this->form_position)
            ->whereIn("type", collect(config("ffhs_custom_forms.custom_field_types"))
                ->filter(fn(string $type) => (new $type()) instanceof CustomLayoutType)
                ->map(fn(string $type) => $type::getFieldIdentifier())
            );


        $query = $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
            ->where("form_position",">", $this->form_position)
            ->where("form_position","<=", $this->layout_end_position)
            ->whereNotIn("id",
                $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                    ->select("id")
                    ->rightJoinSub($subQueryAlLayouts, 'sub', function ($join) {
                        $join->on('custom_fields.form_position', '>', 'sub.form_position')
                            ->on('custom_fields.form_position', '<=', 'sub.layout_end_position');
                    })
            );

        return $query;
    }


}
