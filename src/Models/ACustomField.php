<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use App\Domain\CustomField\CustomFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

abstract class ACustomField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "custom_fields";

    public function getTypeName():string{
        if(!$this->isInheritFromGeneralField()) $typeName = $this->type;
        else $typeName = $this->generalField()->get()->type;
        return  $typeName;
    }
    public function getTypeClass():string{
        return CustomFieldType::getTypeClassFromName($this->getTypeName());
    }
    public function getType():string{
        $typeClass = CustomFieldType::getTypeClassFromName($this->getTypeName());
        return new $typeClass();
    }

    public static function cached(mixed $custom_field_id): ?ACustomField{
        return Cache::remember("custom_field-" .$custom_field_id, 1, fn()=>GeneralField::query()->firstWhere("id", $custom_field_id));
    }

}
