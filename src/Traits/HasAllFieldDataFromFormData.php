<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait HasAllFieldDataFromFormData
{
    use CanLoadCustomFormEditorData;
    use CanLoadFieldRelationFromForm;

    protected function getFieldDataFromFormData(array $fields, CustomForm $customForm): array
    {
        //Get the templated FormComponents
        $fieldsFromTemplate = collect($fields)
            ->whereNotNull('template_id')
            ->flatMap(fn($templateData) => CustomForms::getCustomFormFromId($templateData['template_id'])->customFields)
            ->mapWithKeys(function (CustomField $customField) use ($customForm) {
                $customField = $this->loadFieldRelationsFromForm($customField, $customForm);

                return [$customField->identifier() => $this->loadEditorField($customField)];
            });

        return collect($fields)
            ->mapWithKeys(function (array $field) use ($customForm) {
                $customField = app(CustomField::class)->fill($field);
                $customField = $this->loadFieldRelationsFromForm($customField, $customForm);

                return [$customField->identifier() => $field];
            })
            ->merge($fieldsFromTemplate)
            ->toArray();
    }
}
