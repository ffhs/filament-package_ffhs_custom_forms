<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $custom_form_answer_id
 * @property int $custom_field_id
 * @property CustomFormAnswer $customFormAnswer
 * @property CustomForm $customForm
 * @property CustomField $customField
 * @property array $answer
 */
class CustomFieldAnswer extends CachedModel
{
    private array $data = [];

    protected $fillable = [
        'custom_form_answer_id',
        'custom_field_id',
        'answer'
    ];


    protected $casts = [
        'answer'=>'array',
    ];

    protected array $cachedRelations = [
        "customField" => ["custom_field_id", "id"],
        "customFormAnswer" => ["custom_form_answer_id", "id"],
    ];

    protected array $cachedManyRelations = [
        'customForm'
    ];



    public function __get($key) {
        if($key != "customForm") return parent::__get($key);;
        if(!array_key_exists("customForm",$this->data)) $this->data["customForm"] = $this->customForm()->first();
        return $this->data["customForm"];
    }

    public function customForm(): Builder {
        return CustomForm::query()->whereIn("id", $this->belongsTo(CustomField::class)->select("custom_form_id"));
    }

    public function cachedCustomForm(): CustomForm {
        return $this->customFormAnswer->customForm ;
    }

    public function customField (): BelongsTo {
        return $this->belongsTo(CustomField::class);
    }

    public function customFormAnswer (): BelongsTo {
        return $this->belongsTo(CustomFormAnswer::class);
    }

}
