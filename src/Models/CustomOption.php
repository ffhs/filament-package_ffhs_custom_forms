<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property string $identifier
 * @property string|null $icon
 *
 * @property CustomOption $customOption
 */
class CustomOption extends CachedModel
{
    use HasFormIdentifier;
    use HasFactory;
    use HasTranslations;

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
