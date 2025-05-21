<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait HasAllFieldDataFromFormData
{

    protected function getFieldDataFromFormData(array $fields): array
    {
        //Get the templated FormComponents
        $fieldsFromTemplate = collect($fields)
            ->whereNotNull("template_id")
            ->flatMap(fn($templateData) => CustomForm::cached($templateData["template_id"])->customFields)
            ->mapWithKeys(fn(CustomField $customField) => [
                $customField->identifier() => CustomForms::loadEditorField($customField)
            ]);

        $fields = collect($fields)->mapWithKeys(fn(array $field) => [
            (new CustomField())->fill($field)->identifier() => $field
        ])->merge($fieldsFromTemplate);

        return $fields->toArray();
    }

}
