<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Exception;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldDataHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldHasNoOrWrongCustomFieldTypeException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

trait CanInteractionWithFieldTypes
{
    /**
     * @throws FieldHasNoOrWrongCustomFieldTypeException
     * @throws FieldDataHasNoOrWrongCustomFieldTypeException
     */
    public function getFieldTypeFromRawDate(
        array &$data,
        CustomForm|CustomFormConfiguration $configuration
    ): CustomFieldType {
        if (empty($data)) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        if ($configuration instanceof CustomForm) {
            $configuration = $configuration->getFormConfiguration();
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
            /**@var GeneralField $generalField */
            $generalField = $configuration
                ->getAvailableGeneralFields()
                ->firstWhere('id', $data['general_field_id']);

            /**@phpstan-ignore-next-line */
            return $generalField->getType();
        }

        if (empty($data['type'])) {
            throw new FieldDataHasNoOrWrongCustomFieldTypeException($data);
        }

        return CustomFieldType::getTypeFromIdentifier($data['type']);
    }
}
