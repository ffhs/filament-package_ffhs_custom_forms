<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanGetUsedGeneralFields
{
    protected function getUsedGeneralFieldIds(array $customFields, CustomFormConfiguration $configuration): array
    {
        //GeneralFieldIds From GeneralFields
        $generalFields = array_filter($customFields, fn($fieldData) => !empty($fieldData['general_field_id']));
        $generalFieldId = array_map(fn($used) => $used['general_field_id'], $generalFields);

        //GeneralFieldIds From Templates
        $templateData = array_filter($customFields, fn($fieldData) => !empty($fieldData['template_id']));
        $templateIds = array_map(fn($used) => $used['template_id'], $templateData);

        foreach ($templateIds as $templateId) {
            /**@var CustomForm $template */
            $template = $configuration->getAvailableTemplates()->find($templateId);

            $genFields = $template
                ->getOwnedFields()
                ->pluck('id')
                ->toArray();

            $generalFieldId = [
                ...$generalFieldId,
                ...$genFields,
            ];
        }

        return $generalFieldId;
    }
}
