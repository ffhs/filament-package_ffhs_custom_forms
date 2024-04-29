<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int|null $general_field_id
 * @property int|null $template_id
 * @property int  $custom_form_id
 * @property bool $required
 * @property bool $is_active
 * @property int $form_position
 * @property int|null $layout_end_position
 *
 * @property Collection|null $allCustomFieldsInLayout
 * @property Collection $customOptions
 * @property Collection $fieldRules
 * @property Collection $answers
 * @property Collection $customFieldInLayout
 *
 * @property string|null $identify_key
 * @property array $options
 *
 * @property CustomForm $customForm
 * @property CustomForm|null $template
 * @property GeneralField|null $generalField
*/

class CustomField extends ACustomField
{
    use HasFactory;

    protected $table = "custom_fields";

    protected $fillable = [
        'tool_tip_de',
        'tool_tip_en',
        'name_de',
        'name_en',
        'type',
        'general_field_id',

        'required',
        'is_active',
        'options',
        'custom_form_id',
        'form_position',
        'layout_end_position',
        'identify_key',
        'template_id',
    ];

    protected $casts = [
        "options" => "array",
    ];

    public function __get($key) {
        if($key === "general_field_id") return parent::__get($key);
        if(!$this->isGeneralField()) return parent::__get($key);

        switch ($key){
            case 'name_de':
                return $this->generalField->name_de;
            case 'name_en':
                return $this->generalField->name_en;
            case 'tool_tip_de':
                return $this->generalField->tool_tip_de;
            case 'tool_tip_en':
                return $this->generalField->tool_tip_en;
            case 'type':
                return $this->generalField->type;
            case 'identify_key':
                return $this->generalField->identify_key;
        }

        return parent::__get($key);
    }

    protected static function booted() {
        parent::booted();
        self::creating(function (CustomField $field){#
            //Set identifier key to on other
            if(is_null($field->identify_key) && !$field->isGeneralField() ) {
                $field->identify_key = uniqid();
            }
            return $field;
        });
    }


    private function getInheritStateFromArrays($thisValues, $generalFieldArray): array {
        if(is_null($generalFieldArray)) return $thisValues;
        $output= array_replace($thisValues, array_filter($generalFieldArray, fn($value) => !is_null($value)));
        unset($output["id"]);
        unset($output["general_field_id"]);
        unset($output["created_at"]);
        unset($output["updated_at"]);
        return $output; //ToDo Merge Options (Or overwrite)
    }


    /**
     * @return array there are the stat from this Field and the Stats from the GeneralField
     */
    public function getInheritState():array{
        $generalFiledArray = $this->isGeneralField()?$this->generalField->toArray():null;
        return $this->getInheritStateFromArrays($this->toArray(), $generalFiledArray);
    }

    /**
     * @return array there are the stat from this Field and the Stats from the GeneralField
     */
    public function getInheritStatsFromOrigin(): array {
        $generalFieldId = $this->getOriginal("general_field_id");
        $generalFieldArray = is_null($generalFieldId)? null: GeneralField::cached($generalFieldId)->toArray();
        return $this->getInheritStateFromArrays($this->getOriginal(), $generalFieldArray);
    }


    //Custom Form
    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }
    public static function cached(mixed $custom_field_id): ?CustomField{
        return Cache::remember("custom_field-" .$custom_field_id, config('ffhs_custom_forms.cache_duration'), fn()=>CustomField::query()->firstWhere("id", $custom_field_id));
    }
    public static function cachedAllInForm(int $formId): Collection{
        return Cache::remember("custom_field-form_id" .$formId, config('ffhs_custom_forms.cache_duration'), fn()=>CustomForm::cached($formId)->cachedFields());
    }


    //Options
    public function customOptions (): BelongsToMany {
        return $this->belongsToMany(CustomOption::class, "option_custom_field");
    }


    //GeneralField
    public function isGeneralField():bool{
        return !is_null($this->general_field_id);
    }

    public function fieldRules():HasMany {
        return $this->hasMany(FieldRule::class)->orderBy('execution_order');
    }

    public function isInheritFromGeneralField():bool{
        return !is_null($this->general_field_id);
    }

    public function generalField(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
    }
    public function answers(): HasMany
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }


    //Template
    public function isTemplate(): bool {
        return !empty($this->template_id);
    }
    public function template(): BelongsTo {
        return $this->belongsTo(CustomForm::class, "template_id");
    }



    //Layout
    public function allCustomFieldsInLayout(): HasMany {
        if(!($this->getType() instanceof CustomLayoutType))
            return $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                ->where("id",null);
        return $this->hasMany(CustomField::class, "custom_form_id", "custom_form_id")
            ->where("form_position", ">", $this->form_position)
            ->where("form_position", "<=", $this->layout_end_position);
    }

    public function customFieldInLayout(): HasMany {

        if(!($this->getType() instanceof CustomLayoutType))
            return $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                ->where("id",null);


        $subQueryAlLayouts =
            $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                ->select('form_position','layout_end_position')
                ->where("form_position",">", $this->form_position)
                ->where("layout_end_position","!=", null);


        return $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
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
    }


}
