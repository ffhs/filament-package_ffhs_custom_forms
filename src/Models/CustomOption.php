<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name_de
 * @property string|null $name_en
 * @property string|null $custom_key
 */
class CustomOption extends Model
{
    use HasFormIdentifyer;
    use HasFactory;


    protected $fillable = [
        'name_de',
        'name_en',
        'custom_key',
    ];

    protected static function booted() {
        parent::booted();

        self::created(function(CustomOption $customForm) {
            if(!$customForm->getFormConfiguration()::hasVariations()) return;
            if(!is_null($customForm->relation_model_id)) return;
            $customForm->customForm()->save($customForm);
            $customForm->save();
        });

    }

}
