<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name_de
 * @property string $name_en
 * @property string $identifier
 * @property string|null $icon
 *
 * @property CustomOption $customOption
 */
class CustomOption extends Model
{
    use HasFormIdentifier;
    use HasFactory;

    protected $fillable = [
        'name_de',
        'name_en',
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
