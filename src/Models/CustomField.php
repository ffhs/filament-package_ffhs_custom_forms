<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestingObject;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers\Identifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * property Collection $fieldRules
 * @property Collection $answers
 *
 * @property string|null $identifier
 *
 * @property CustomForm $customForm
 * @property CustomForm|null $template
 * @property GeneralField|null $generalField
*/
class CustomField extends ACustomField implements NestingObject , Identifier
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
        'customOptions',
        "customForm",
        "generalField",
        "template",
    ];

    public static function getPositionAttribute(): string
    {
        return 'form_position';
    }

    public static function getEndContainerPositionAttribute(): string
    {
        return 'layout_end_position';
    }


    //Custom Form

    protected static function booted(): void {
        parent::booted();

        self::creating(function (CustomField $field){#
            //Set identifier key to on other
            if(empty($field->identifier()) && !$field->isGeneralField()) $field->identifier = uniqid();
            return $field;
        });
    }


    //Options

    public function identifier(): string
    {
        return $this->__get("identifier");
    }


    //GeneralField

    public function __get($key) {

        if($key === "general_field_id") {
            return parent::__get($key);
        }

        if(!$this->isGeneralField()) {
            if('overwritten_options' === $key) return [];
            return parent::__get($key);
        }


        //PERFORMANCE!!!!
        $genFieldFunction = function(): GeneralField {
            if(!$this->exists) return parent::__get("generalField");
            $genField = GeneralField::getModelCache()?->where("id",$this->general_field_id,)->first();
            if(!is_null($genField)) return $genField;

            $generalFieldIds = $this->customForm->customFields->whereNotNull('general_field_id')->pluck("general_field_id");
            $generalFields = GeneralField::query()->whereIn("id",$generalFieldIds)->get();
            GeneralField::addToModelCache($generalFields);
            return GeneralField::cached($this->general_field_id);
        };


        return match ($key) {
            'name' => $genFieldFunction()->name,
            'type' => $genFieldFunction()->type,
            'options' => array_merge(parent::__get($key)??[], $genFieldFunction()->overwrite_options??[]),
            'overwritten_options' => array_keys($genFieldFunction()->overwrite_options??[]),
            'identifier' => $genFieldFunction()->identifier,
            'generalField' => $genFieldFunction(),
            default => parent::__get($key),
        };

    }

    public function isGeneralField():bool{
        return !empty($this->general_field_id);
    }

    public function customForm(): BelongsTo {
        return $this->belongsTo(CustomForm::class);
    }

    public function customOptions (): BelongsToMany {
        return $this->belongsToMany(CustomOption::class, "option_custom_field");
    }


    //Template

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

    public function isTemplate(): bool {
        if(!isset($this->template_id)) return false;
        return !empty($this->template_id);
    }

    public function template(): BelongsTo {
        return $this->belongsTo(CustomForm::class, "template_id");
    }
}
