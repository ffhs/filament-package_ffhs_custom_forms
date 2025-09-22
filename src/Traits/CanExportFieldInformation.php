<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\FlattedNestedList\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Illuminate\Support\Collection;

trait CanExportFieldInformation
{
    public function exportFieldInformation(Collection $customFields): array
    {
        $customFields = $customFields->keyBy(fn(CustomField $field) => $field->identifier);

        $flattenList = NestedFlattenList::make($customFields, CustomField::class);
        $structure = $flattenList->getStructure(true);

        return $this->exportFields($structure, $customFields);
    }

    public function exportFields(array $structure, Collection $customFields): array //ToDo fix for embedded forms
    {
        $exportedFields = [];

        foreach ($structure as $customFieldIdentifier => $subStructure) {
            /**@var EmbedCustomField $field */
            $field = $customFields->get($customFieldIdentifier);
            $rawFieldData = $field->toArray();
            $fieldData = [];

            if (!empty($rawFieldData['options'])) {
                $fieldData['options'] = $rawFieldData['options'] ?? [];
            }

            if (!is_null($field->template_id)) {
                $fieldData['identifier'] = $customFieldIdentifier;
                $fieldData['template'] = $field->template->template_identifier;
            } elseif (!is_null($field->general_field_id)) {
                $fieldData['general_field'] = $customFieldIdentifier;
            } else {
                $fieldData['identifier'] = $customFieldIdentifier;
                $fieldData['type'] = $field->type;
                $fieldData['name'] = $rawFieldData['name'] ?? null;

                //Options
                if ($field->getCustomOptions()->isNotEmpty()) {
                    $fieldData['customOptions'] = $field
                        ->getCustomOptions()
                        ->map(function (CustomOption $option) {
                            $optionData = $option->toArray();
                            $optionData['name'] = $option->translations['name'];
                            return $optionData;
                        })
                        ->toArray();
                }
            }

            if (!empty($subStructure)) {
                $fieldData['fields'] = $this->exportFields($subStructure, $customFields);
            }

            $exportedFields[] = $fieldData;
        }

        return $exportedFields;
    }
}
