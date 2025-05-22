<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldDataHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

trait CanInteractionWithFieldTypes
{
    /**
     * @throws FieldHasNoOrWrongCustomFieldTypeException
     * @throws FieldDataHasNoOrWrongCustomFieldTypeException
     */
    public function getFieldTypeFromRawDate(array $data): CustomFieldType
    {
        if (empty($data)) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        //ToDo modify to make it variable
        if (!empty($data["template_id"])) {
            return TemplateFieldType::make();
        }

        if (!empty($data["general_field_id"])) {
            return GeneralField::cached($data["general_field_id"])->getType();
        }

        if (empty($data["type"])) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        return CustomFieldType::getTypeFromIdentifier($data["type"]);
    }


}
