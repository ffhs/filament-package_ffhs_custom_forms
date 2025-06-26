<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

trait CanImportFieldInformation
{
    public function importFields(
        array $rawFields,
        CustomForm $customForm,
        array $templateMap = [],
        array $generalFieldMap = []
    ): void {
        $templateImportedMap = CustomForm::query()
            ->whereNot('template_identifier')
            ->pluck('id', 'template_identifier')
            ->toArray();
        $templateMap = array_merge($templateImportedMap, $templateMap);

        $generalFieldImportedMap = GeneralField::query()
            ->pluck('id', 'identifier')
            ->toArray();
        $generalFieldMap = array_merge($generalFieldImportedMap, $generalFieldMap);

        $fields = $this->importFieldDatas($rawFields, $customForm, $templateMap, $generalFieldMap);


        foreach ($fields as $field) { //ToDo optimize
            $options = $field['customOptions'] ?? [];
            unset($field['customOptions']);
            $field = new CustomField($field);
            $field->save();
            if (!empty($options)) {
                $field->customOptions()->saveMany($options);
            }
        }
    }

    public function importFieldDatas(
        array $rawFields,
        CustomForm $customForm,
        array $templateMap = [],
        array $generalFieldMap = [],
        int &$fieldCounter = 0
    ): array {
        $fieldsToCreate = [];
        foreach ($rawFields as $rawField) {
            $fieldCounter++;
            $field = [
                'form_position' => $fieldCounter,
                'custom_form_id' => $customForm->id,
            ];

            if (array_key_exists('fields', $rawField)) {
                $subFields = $rawField['fields'];
                $subFieldData = $this->importFieldDatas($subFields, $customForm, $templateMap, $generalFieldMap,
                    $fieldCounter);
                $fieldsToCreate = [...$fieldsToCreate, ...$subFieldData];
                $field['layout_end_position'] = $fieldCounter;
                unset($rawField['fields']);
            }

            if (array_key_exists('template', $rawField)) {
                $template = $rawField['template'];
                $field['template_id'] = $templateMap[$template] ?? $template;
                unset($rawField['template']);
            }

            if (array_key_exists('general_field', $rawField)) {
                $generalField = $rawField['general_field'];
                $field['general_field_id'] = $generalFieldMap[$generalField] ?? $generalField;
                unset($rawField['general_field']);
            }

            if (array_key_exists('customOptions', $rawField)) {
                $customOptions = array_map(
                    static fn($item) => new CustomOption($item),
                    $rawField['customOptions']
                );
                $field['customOptions'] = $customOptions;
            }

            $field = [...$rawField, ...$field];

            $fieldsToCreate[] = $field;
        }

        return $fieldsToCreate;
    }
}
