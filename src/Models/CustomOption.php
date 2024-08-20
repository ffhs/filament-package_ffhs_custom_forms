<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property array $name
 * @property string $identifier
 * @property string|null $icon
 *
 * @property CustomOption $customOption
 */
class CustomOption extends Model implements CachedModel
{
    use HasFormIdentifier;
    use HasTranslations;
    use HasCacheModel;
    use HasFactory;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'identifier',
        'icon',
    ];


    protected static function booted() {
        parent::booted();

        self::creating(function(CustomOption $customOption) {
            if(is_null($customOption->identifier)) $customOption->identifier= uniqid();
        });
    }


}
