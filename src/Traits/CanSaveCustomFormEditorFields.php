<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Support\Collection;

trait CanSaveCustomFormEditorFields
{
    public function updatingCustomFields(
        array $fieldsToCreateData,
        array $fieldsToSaveData,
        array $usedIds,
        CustomForm $form,
        Collection $rawData
    ): void {
        //Create and Updating
        CustomField::upsert($fieldsToSaveData, ['id']);
        CustomField::insert($fieldsToCreateData);

        $savedFields = $form
            ->ownedFields()
            ->get();
        $generalFields = $form
            ->getFormConfiguration()
            ->getAvailableGeneralFields();

        $rawData = $rawData->mapWithKeys(function (array $fieldData) use ($generalFields) {
            if (!isset($fieldData['general_field_id'])) {
                return [$fieldData['identifier'] => $fieldData];
            }

            /**@phpstan-var null|GeneralField $genField */
            $genField = $generalFields->firstWhere('id', $fieldData['general_field_id']);

            /** @phpstan-ignore-next-line */
            return [$genField?->identifier => $fieldData];
        });

        //Run after Save
        $savedFields
            ->whereIn('id', collect($fieldsToSaveData)->pluck('id'))
            ->each(function (CustomField $field) use ($rawData): void {
                $data = $rawData->get($field->identifier, []);
                $field->getType()->doAfterSaveField($field, $data);
            });

        //Run after Create
        $savedFields
            ->whereNotIn('id', $usedIds)
            ->each(function (CustomField $field) use ($rawData): void {
                $data = $rawData->get($field->identifier, []);
                $field
                    ->getType()
                    ->doAfterCreateField($field, $data);
            });
    }

    protected function saveFields(array $rawData, CustomForm $form): void
    {
        $rawFields = collect($rawData);
        $oldFields = $form->getOwnedFields();

        [$fieldsToSaveData, $fieldsToCreate, $usedIds] = $this->prepareFields($rawData, $oldFields, $form);

        //Deleting Fields
        $this->deletingFields($usedIds, $oldFields);

        //cleanUp
        $fieldsToCreate = $this->cleanUpCustomFieldData($fieldsToCreate);
        $fieldsToSaveData = $this->cleanUpCustomFieldData($fieldsToSaveData);

        //Create and Updating
        $this->updatingCustomFields($fieldsToCreate, $fieldsToSaveData, $usedIds, $form, $rawFields);
    }

    private function prepareFields(array $rawFields, Collection $oldFields, CustomForm $form): array
    {
        $oldFields = $oldFields->keyBy('id');
        $fieldsToSaveData = [];
        $fieldsToCreate = [];
        $usedIds = [];
        $now = now();

        //Prepare Save and Create Data
        foreach ($rawFields as $field) {
            $field['custom_form_id'] = $form->id;

            if (!empty($field['id'])) {
                $usedIds[] = $field['id'];
                $customField = $oldFields->get($field['id']) ?? app(CustomField::class);

                if (!$customField->id) {
                    unset($field['id']);
                }
            } else {
                $customField = app(CustomField::class);
            }

            $type = CustomForms::getFieldTypeFromRawDate($field, $form->getFormConfiguration());
            $mutatedFieldData = $type->getMutateCustomFieldDataOnSave($customField, $field);

            $customField->fill($mutatedFieldData);

            $type->doBeforeSaveField($customField, $mutatedFieldData);

            if (!$customField->exists) {
                $rawField = $customField->getAttributes();
                $rawField['created_at'] = $now;
                $rawField['updated_at'] = $now;
                $fieldsToCreate[] = $rawField;
            } elseif ($customField->isDirty()) {
                $rawField = $customField->getAttributes();
                $rawField['updated_at'] = $now;
                $fieldsToSaveData[] = $rawField;
            }
        }

        return array($fieldsToSaveData, $fieldsToCreate, $usedIds);
    }

    private function deletingFields(array $usedIds, Collection $oldFields): void
    {
        $fieldsToDelete = $oldFields->whereNotIn('id', $usedIds);
        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doBeforeDeleteField($field));
        CustomField::destroy($fieldsToDelete->pluck('id'));
        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doAfterDeleteField($field));
    }

    private function cleanUpCustomFieldData($fields): array
    {
        $cleanFields = [];
        $columns = [
            'created_at',
            'updated_at',
            'id',
            ...app(CustomField::class)->getFillable()
        ];

        foreach ($fields as $key => $field) {
            foreach ($columns as $name) {
                $cleanFields[$key][$name] = $field[$name] ?? null;
            }
        }

        return $cleanFields;
    }
}
