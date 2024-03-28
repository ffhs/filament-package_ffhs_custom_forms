<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 *
 * @property string|null $name_de
 * @property string|null $name_en
 *
 * @property string|null tool_tip_de
 * @property string|null tool_tip_en
 *
 * @property String|null $type
 */
abstract class ACustomField extends Model
{
    use HasFactory;
    //use SoftDeletes;



    public function getTypeName():?string{
        if($this->getTable() == "general_fields") $typeName = $this->type;
        else if(!$this->isInheritFromGeneralField()) $typeName = $this->type;
        else $typeName = $this->generalField->type;
        return  $typeName;
    }
    public function getTypeClass():?string{
        if($this instanceof CustomField && $this->isTemplate()) return TemplateFieldType::class;
        $typeName = $this->getTypeName();
        if(is_null($typeName)) return null;
        return CustomFieldType::getTypeClassFromName($typeName);
    }
    public function getType():?CustomFieldType{
        if($this instanceof CustomField && $this->isTemplate()) return new TemplateFieldType();

        $typeName = $this->getTypeName();
        if(is_null($typeName)) return null;
        $typeClass = CustomFieldType::getTypeClassFromName($this->getTypeName());
        return new $typeClass();
    }

    public static function cached(mixed $custom_field_id): ?ACustomField{
        return Cache::remember("custom_field-" .$custom_field_id, config('ffhs_custom_forms.cache_duration'), fn()=>GeneralField::query()->firstWhere("id", $custom_field_id));
    }

}
