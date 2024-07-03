<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int custom_field_id
 * @property int execution_order
 *
 * @property string rule_identifier
 * @property string anchor_identifier
 * @property string rule_name
 * @property CustomField customField
 *
 * @property array anchor_data
 * @property array rule_data
 */
class FieldRule extends Model implements CachedModel
{
    use HasCacheModel;
    use HasFormIdentifier;
    use HasFactory;

    protected array $cachedRelations = [
        "customField"  => ["custom_field_id","id"],
    ];

    protected $fillable = [
        'custom_field_id',
        'anchor_identifier',
        'anchor_data',
        'rule_identifier',
        'rule_data',
        'execution_order'
    ];


    protected $casts = [
        'anchor_data'=>'array',
        'rule_data'=>'array',
    ];

    public function customField ():BelongsTo {
        return $this->belongsTo(CustomField::class);
    }

    public function getAnchorType() :FieldRuleAnchorType{
        return FieldRuleAnchorType::getTypeFromIdentifier($this->anchor_identifier);
    }

    public function getRuleType() :FieldRuleType{
        return FieldRuleType::getTypeFromIdentifier($this->rule_identifier);
    }

}
