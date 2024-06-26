<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property GeneralField $generalField
 * @property string $custom_form_identifier
 * @property int $general_field_id
 * @property bool $is_required
 * @property bool $export
 */
class GeneralFieldForm extends CachedModel
{

    use HasFormIdentifier;

    protected $table = "general_field_form";
    protected $fillable = [
        'general_field_id',
        'custom_form_identifier',
        'is_required',
        'export',
    ];


    protected array $cachedRelations = [
        "generalField" => ["general_field_id", "id"],
    ];

    public function generalField(): BelongsTo {
        return $this->belongsTo(GeneralField::class);
    }

    public static function getFromFormIdentifier($formIdentifier): Collection {
        return Cache::remember("general_filed_form-from-identifier_".$formIdentifier, 5,
            fn() => GeneralFieldForm::query()
                ->where("custom_form_identifier", $formIdentifier)
                ->get()

        );
    }


    public static function getGeneralFieldQuery(string $identifier): Builder {
        return GeneralField::query()->whereIn("id",
            GeneralFieldForm::query()
                ->select("general_field_id")
                ->where("custom_form_identifier",$identifier)
        );
    }


}
