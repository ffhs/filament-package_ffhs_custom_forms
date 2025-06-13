<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldHasNoOrWrongCustomFieldTypeException;
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
abstract class ACustomField extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name'];

    /**
     * @throws FieldHasNoOrWrongCustomFieldTypeException
     */
    public function getType(): CustomFieldType
    {
        if ($this instanceof CustomField && $this->isTemplate()) {
            return new TemplateFieldType();
        }

        $typeClass = $this->getTypeClass();
        if (is_null($typeClass)) {
            throw new FieldHasNoOrWrongCustomFieldTypeException($this->type);
        }
        return $this->getTypeClass()::make();
    }

    public function getTypeClass(): ?string
    {
        if ($this instanceof CustomField && $this->isTemplate()) {
            return TemplateFieldType::class;
        }
        $typeName = $this->type;
        if (is_null($typeName)) {
            return null;
        }
        return CustomFieldType::getTypeClassFromIdentifier($typeName);
    }
}
