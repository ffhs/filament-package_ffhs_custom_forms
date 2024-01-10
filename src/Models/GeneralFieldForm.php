<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property GeneralField $generalField
 * @property bool $is_required
 */
class GeneralFieldForm extends Model
{

    use HasFormIdentifyer;

    protected $table = "general_field_form";
    protected $fillable = [
        'general_field_id',
        'custom_form_identifier',
        'is_required',
    ];

    public function generalField(): BelongsTo {
        return $this->belongsTo(GeneralField::class);
    }



}
