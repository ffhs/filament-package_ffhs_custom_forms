<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormLoadHelper;
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
                $customField->identifier() => array_merge( EditCustomFormLoadHelper::loadField($customField))
            ])
            ->toArray();
        return array_merge($fieldsFromTemplate, $fields);
    }

}
