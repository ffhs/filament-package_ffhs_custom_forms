<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;

trait CanSaveCustomFormEditorFields
{
    protected function saveFields($custom_fields, CustomForm $form): void
    {
        $rawFields = $custom_fields;
        $oldFields = $form->getOwnedFields();

        [$fieldsToSaveData, $fieldsToCreate, $usedIds] = $this->prepareFields($rawFields, $oldFields, $form);

        //Deleting Fields
        $this->deletingFields($usedIds, $oldFields);

        //cleanUp
        $fieldsToCreate = $this->cleanUpCustomFieldData($fieldsToCreate);
        $fieldsToSaveData = $this->cleanUpCustomFieldData($fieldsToSaveData);

        //Create and Updating
        $this->updatingCustomFields($fieldsToCreate, $fieldsToSaveData, $usedIds, $form);
    }

    private function prepareFields(mixed $rawFields, Collection $oldFields, CustomForm $form): array
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

            $type = CustomForms::getFieldTypeFromRawDate($field);
            $field = $type->getMutateCustomFieldDataOnSave($customField, $field);
            $customField->fill($field);
            $type->doBeforeSaveField($customField, $field);

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


    public function updatingCustomFields(
        array $fieldsToCreateData,
        array $fieldsToSaveData,
        array $usedIds,
        CustomForm $form
    ): void {
        $fieldsToSave = collect($fieldsToSaveData)->keyBy('id');
        $fieldsToCreate = collect($fieldsToCreateData)->keyBy('identifier');

        //Create and Updating
        CustomField::upsert($fieldsToSaveData, ['id']);
        CustomField::insert($fieldsToCreateData);

        $savedFields = $form->ownedFields()->get();

        //Run after Save
        $savedFields
            ->whereIn('id', $fieldsToSave->keys())
            ->each(function (CustomField $field) use ($fieldsToSave): void {
                $data = $fieldsToSave->get($field->id) ?? [];
                $field->getType()->doAfterSaveField($field, $data);
            });

        //Run after Create
        $savedFields
            ->whereNotIn('id', $usedIds)
            ->each(function (CustomField $field) use ($fieldsToCreate): void {
                $data = $fieldsToCreate->get($field->identifier) ?? [];
                $field->getType()->doAfterCreateField($field, $data);
            });
    }
}
