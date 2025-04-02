<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\HasFormIdentifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * 
 *
 * @property int $id
 * @property int $general_field_id
 * @property string $custom_form_identifier
 * @property int $is_required
 * @property int $export
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField $generalField
 * @property-read string $cache_key_for
 * @method static Builder<static>|GeneralFieldForm newModelQuery()
 * @method static Builder<static>|GeneralFieldForm newQuery()
 * @method static Builder<static>|GeneralFieldForm query()
 * @method static Builder<static>|GeneralFieldForm whereCreatedAt($value)
 * @method static Builder<static>|GeneralFieldForm whereCustomFormIdentifier($value)
 * @method static Builder<static>|GeneralFieldForm whereExport($value)
 * @method static Builder<static>|GeneralFieldForm whereGeneralFieldId($value)
 * @method static Builder<static>|GeneralFieldForm whereId($value)
 * @method static Builder<static>|GeneralFieldForm whereIsRequired($value)
 * @method static Builder<static>|GeneralFieldForm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GeneralFieldForm extends Model implements CachedModel
{
    use HasCacheModel;

    use HasFormIdentifier;

    protected $table = "general_field_form";
    protected $fillable = [
        'general_field_id',
        'custom_form_identifier',
        'is_required',
        'export',
    ];

    protected array $cachedRelations = [
        "generalField",
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
