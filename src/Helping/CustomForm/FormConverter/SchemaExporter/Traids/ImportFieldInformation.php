<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

trait ImportFieldInformation
{


    public function createImportFields(array $rawFields,  CustomForm $customForm, array $templateMap = [], array $generalFieldMap = []): void
    {
        $templateImportedMap = CustomForm::query()
            ->whereNot("template_identifier")
            ->pluck('id', 'template_identifier')
            ->toArray();
        $templateMap = array_merge($templateImportedMap, $templateMap);

        $generalFieldImportedMap = GeneralField::query()
            ->pluck('id', 'identifier')
            ->toArray();
        $generalFieldMap = array_merge($generalFieldImportedMap, $generalFieldMap);

        $fields = $this->importFields($rawFields, $customForm, $templateMap, $generalFieldMap);
        CustomField::upsert($fields);

    }

    public function importFields(array $rawFields,  CustomForm $customForm, array $templateMap = [], array $generalFieldMap = [], int &$fieldCounter = 1): array
    {
        $fieldsToCreate = [];
        foreach ($rawFields as $rawField) {
            $field = [
                'form_position' => $fieldCounter,
                'custom_form_id' => $customForm->id,
            ];
            $fieldCounter++;

            if(array_key_exists('fields', $rawField)){
                $subFields = $rawField["fields"];
                $subFieldData = $this->importFields($subFields, $customForm, $templateMap, $generalFieldMap, $fieldCounter);
                $fieldsToCreate = array_merge($fieldsToCreate, $subFieldData);
                $field["layout_end_position"] = $fieldCounter;
                unset($rawField["fields"]);
            }


            if(array_key_exists('template', $rawField)){
                $template = $rawField['template'];
                $field['template_id'] = $templateMap[$template];
                unset($rawField["template"]);
            }

            if(array_key_exists('general_field', $rawField)){
                $generalField = $rawField['general_field'];
                $field['general_field_id'] = $generalFieldMap[$generalField];
                unset($rawField["general_field"]);
            }

            $field = array_merge($rawField, $field);


            $fieldsToCreate[] = $field;

        }

        $fieldCounter++;
        return $fieldsToCreate;
    }

}
