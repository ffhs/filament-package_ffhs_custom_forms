<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanLoadFieldRelationFromForm
{
    protected function loadFieldRelationsFromForm(CustomField $customField, CustomForm $customForm): CustomField
    {
        if ($customField->isGeneralField() && !$customField->relationLoaded('generalField')) {
            $genField = $customForm
                ->getFormConfiguration()
                ->getAvailableGeneralFields()
                ->get($customField->general_field_id);
            $customField->setRelation('generalField', $genField);
        }
        if ($customField->custom_form_id === $customForm->id && !$customField->relationLoaded('customForm')) {
            $customField->setRelation('customForm', $customForm);
        } elseif (!$customField->relationLoaded('customForm')) {
            $form = $customForm->getFormConfiguration()
                ->getAvailableTemplates()
                ->get($customField->custom_form_id);
            if ($form) {
                $customField->setRelation('customForm', $form);
            }
        }
        return $customField;
    }
}
