<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAnchorAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventList;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\HasRule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Trigger\TriggerList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int custom_field_id
 * @property int execution_order
 *
 * @property array event_data
 * @property array trigger_data
 *
 * @property string rule_name
 * @property CustomField customField
 */
class FieldRule extends Model implements CachedModel, Rule
{
    use HasRule;
    use HasCacheModel;
    use HasFormIdentifier;
    use HasFactory;

    protected array $cachedRelations = [
        "customField"  => ["custom_field_id","id"],
    ];

    protected $fillable = [
        'custom_field_id',
        'execution_order',
        'event_data',
        'trigger_data',
    ];


    protected $casts = [
        'anchor_data'=>'array',
        'rule_data'=>'array',
    ];

    public function customField ():BelongsTo {
        return $this->belongsTo(CustomField::class);
    }


    public function getRawEventData(): array
    {
        return $this->event_data;
    }

    public function getRawTriggerData(): array
    {
        return $this->trigger_data;
    }

    public function setTriggerData(TriggerList|array $data): static
    {
        $this->trigger_data = $data;
        return $this;
    }

    public function setEventData(EventList|array $data): static
    {
        $this->event_data = $data;
        return $this;
    }



    public function handle(array $arguments, mixed $target): mixed
    {
        // TODO: Implement handle() method.
        return null;
    }
}
