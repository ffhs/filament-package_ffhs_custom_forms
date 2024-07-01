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
 * @property array $overwritten_options
 *
 * @property Collection $customOptions
 * @property Collection $fieldRules
 * @property Collection $answers
 *
 * @property string|null $identifier
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
        'name',
        'type',
        'general_field_id',
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

        if(!$this->isGeneralField()) {
            if('overwritten_options' === $key) return [];
            return parent::__get($key);
        }

        //PERFORMANCE!!!!
        $genFieldF = function(): GeneralField {
            if(!$this->exists) return parent::__get("generalField");
            $genField = GeneralField::cached($this->general_field_id,"id",false);
            if(!is_null($genField)) return $genField;

            $generalFieldIds = $this->customForm->customFields->whereNotNull('general_field_id')->pluck("general_field_id");
            $generalFields = GeneralField::query()->whereIn("id",$generalFieldIds)->get();
            GeneralField::addToCachedList($generalFields);
            return GeneralField::cached($this->general_field_id,"id",false);
        };

        //ToDo Merge Options (or overwrite)
        return match ($key) {
            'name' => $genFieldF()->name,
            'type' => $genFieldF()->type,
            'options' => array_merge(parent::__get($key), $genFieldF()->overwrite_options),
            'overwritten_options' => array_keys($genFieldF()->overwrite_options),
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


}
