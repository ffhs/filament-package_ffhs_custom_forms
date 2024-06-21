<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $general_field_id
 * @property int|null $template_id
 * @property int  $custom_form_id
 * @property bool $required
 * @property bool $is_active
 * @property int $form_position
 * @property int|null $layout_end_position
 *
 # * @property Collection|null $allCustomFieldsInLayout
 # * @property Collection $customFieldInLayout
 *
 * @property Collection $customOptions
 * @property Collection $fieldRules
 * @property Collection $answers
 *
 * @property string|null $identifier
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
        'tool_tip',
        'name',
        'type',
        'general_field_id',

        'required',
        'is_active',
        'options',
        'custom_form_id',
        'form_position',
        'layout_end_position',
        'identifier',
        'template_id',
    ];

    protected $casts = [
        "options" => "array",
    ];


    protected array $cachedRelations = [
        "customForm" => ["custom_form_id", "id"],
        "generalField" => ["general_field_id", "id"],
        "template" => ["template_id", "id"],
    ];


    protected array $cachedManyRelations = [
        'fieldRules',
        'customOptions',
    ];

    public function __get($key) {
        if($key === "general_field_id") return Model::__get($key);

        if(!$this->isGeneralField()) return parent::__get($key);

        //PERFORMANCE!!!!
        $genFieldF = function(): GeneralField {
            if(!$this->exists) return parent::__get("generalField");
            $genField = GeneralField::cached($this->general_field_id,"id",false);
            if(!is_null($genField)) return $genField;

            $generalFieldIds = $this->customForm->cachedFieldsWithTemplates()->whereNotNull('general_field_id')->pluck("general_field_id");
            $generalFields = GeneralField::query()->whereIn("id",$generalFieldIds)->get();
            GeneralField::addToCachedList($generalFields);
            return GeneralField::cached($this->general_field_id,"id",false);
        };

        //ToDo Merge Options (or overwrite)
        return match ($key) {
            'name' => $genFieldF()->name,
            'tool_tip' => $genFieldF()->tool_tip,
            'type' => $genFieldF()->type,
            'identifier' => $genFieldF()->identifier,
            'generalField' => $genFieldF(),
            default => parent::__get($key),
        };

    }

    protected static function booted(): void {
        parent::booted();
        self::creating(function (CustomField $field){#
            //Set identifier key to on other
            if(is_null($field->identifier) && !$field->isGeneralField() ) $field->identifier = uniqid();
            return $field;
        });
    }


    /*private function getInheritStateFromArrays($thisValues, $generalFieldArray): array {
        if(is_null($generalFieldArray)) return $thisValues;
        $output= array_replace($thisValues, array_filter($generalFieldArray, fn($value) => !is_null($value)));
        unset($output["id"]);
        unset($output["general_field_id"]);
        unset($output["created_at"]);
        unset($output["updated_at"]);
        return $output;
    }*/


    /* /**
      * @return array there are the stat from this Field and the Stats from the GeneralField
      */
    /*public function getInheritState():array{

        $generalFiledArray = $this->isGeneralField()?$this->generalField->toArray():null;
        return $this->getInheritStateFromArrays($this->toArray(), $generalFiledArray);
    }*/

    /*/**
     * @return array there are the stat from this Field and the Stats from the GeneralField
     */
    /* public function getInheritStatsFromOrigin(): array {
         $generalFieldId = $this->getOriginal("general_field_id");
         $generalFieldArray = is_null($generalFieldId)? null: GeneralField::cached($generalFieldId)->toArray();
         return $this->getInheritStateFromArrays($this->getOriginal(), $generalFieldArray);
     }*/


    //Custom Form
    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
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

    /**
     * @return array[int, array] int is the continue position, array is the FieldData
     */
    public function loadEditData(): array {
        return $this->getType()->loadEditData($this);
    }


    /*//Layout
     public function allCustomFieldsInLayout(): HasMany { //ToDo i do it to remove? (Not now)
         if(!($this->getType() instanceof CustomLayoutType))
             return $this->hasMany(CustomField::class, "custom_form_id","custom_form_id")
                 ->where("id",null);
         return $this->hasMany(CustomField::class, "custom_form_id", "custom_form_id")
             ->where("form_position", ">", $this->form_position)
             ->where("form_position", "<=", $this->layout_end_position);
     }



     public function customFieldInLayout(): HasMany { //ToDo i do it to remove? (Not now)

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
     }*/



}
