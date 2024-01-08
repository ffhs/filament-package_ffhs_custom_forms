<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

        'is_term_bound',
        'custom_form_id',
        'has_variations',
        'form_position',
    ];


    protected static function booted()
    {
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
    public function templateVariation ():Model|null {
        return $this->customFieldVariations->filter(fn($customFieldVariation)=> $customFieldVariation->isTemplate())->first();
    }

    private function getInheritStateFromArrays($thisValues, $generalFieldArray){
        $output =  $thisValues;
        if(!is_null($generalFieldArray))
            $output= array_replace($output, array_filter($generalFieldArray, fn($value) => !is_null($value)));
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
        return !is_null($this->custom_field_id);
    }

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function generalField(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
    }


    public function getVariation($relatedObject ): Model|null{
        if(!$this->has_variations) return $this->templateVariation();
        $variation =  $this->customFieldVariations()->get()->filter(fn($fieldVariation)=>$fieldVariation->variation_id == $relatedObject->id);
        if(is_null($variation)) return $this->templateVariation();
        else return $relatedObject;
    }


}
