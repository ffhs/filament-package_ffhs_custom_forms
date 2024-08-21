<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 *
 * @property string|null $name
 * @property array $options
 *
 * @property String|null $type
 */
abstract class ACustomField extends Model implements CachedModel
{
    use HasFactory;
    use HasTranslations;
    use HasCacheModel;

    public $translatable = ['name'];


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
