<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Exception;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldDataHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanInteractionWithFieldTypes
{
    /**
     * @throws FieldHasNoOrWrongCustomFieldTypeException
     * @throws FieldDataHasNoOrWrongCustomFieldTypeException
     */
    public function getFieldTypeFromRawDate(array &$data, CustomForm $customForm): CustomFieldType
    {
        if (empty($data)) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        if (!empty($data['cachedFieldType'])) {
            try {
                return $data['cachedFieldType']::make();
            } catch (Exception $e) {
            }
        }

        //ToDo modify to make it variable
        if (!empty($data['template_id'])) {
            return TemplateFieldType::make();
        }

        if (!empty($data['general_field_id'])) {
            return $customForm
                ->getFormConfiguration()
                ->getAvailableGeneralFields()
                ->firstWhere('id', $data['general_field_id'])
                ->getType();
        }

        if (empty($data['type'])) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        return CustomFieldType::getTypeFromIdentifier($data['type']);
    }
}
