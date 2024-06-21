<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 *
 * @property string|null $name
 *
 * @property string|null tool_tip
 *
 * @property String|null $type
 */
abstract class ACustomField extends CachedModel
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name', 'tool_tip'];


    public function getTypeClass():?string{
        if($this instanceof CustomField && $this->isTemplate()) return TemplateFieldType::class;
        $typeName = $this->type;
        if(is_null($typeName)) return null;
        return CustomFieldType::getTypeClassFromIdentifier($typeName);
    }

    public function getType():?CustomFieldType{
        if($this instanceof CustomField && $this->isTemplate()) return new TemplateFieldType();
        $typeClass = $this->getTypeClass();
        if(is_null($typeClass)) return null;
        return $this->getTypeClass()::make();
    }

}
