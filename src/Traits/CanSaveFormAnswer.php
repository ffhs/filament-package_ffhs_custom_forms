<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;

trait CanSaveFormAnswer
{
    public function saveFormAnswer(CustomFormAnswer $formAnswer, Form $form, array $data, string $path = ''): void
    {
        $customForm = $formAnswer->customForm;

        // Mapping and combining custom fields
        $customFieldsIdentify = $customForm
            ->customFields
            ->mapWithKeys(function (CustomField $customField) {
                return [$customField->identifier => $customField];
            });

        //Update form data after modifying components
        $this->prepareFormComponents($customFieldsIdentify, $form, $path);

        // Mapping and combining field answers
        $preparedData = $this->splittingFormComponents($data, $customFieldsIdentify);

        DB::transaction(function () use ($preparedData, $customFieldsIdentify, $formAnswer) {
            $this->saveWithoutPreparation($formAnswer, $customFieldsIdentify, $preparedData);
        });
    }

    protected function getFieldAttributesToSave(CustomFieldAnswer $answer): array
    {
        $attributes = $answer->attributesToArray();

        foreach ($answer->getCasts() as $key => $type) {
            if ($type === 'array' && isset($attributes[$key])) {
                $attributes[$key] = json_encode($attributes[$key], JSON_THROW_ON_ERROR);
            }
        }
        return $attributes;
    }

    protected function splittingFormComponents(
        array $formData,
        Collection $customFieldsIdentify
    ): array {
        $dateSplit = [];
        foreach ($formData as $identifyKey => $customFieldAnswererRawData) {
            /**@var CustomField $customField */
            $customField = $customFieldsIdentify->get($identifyKey);
            if (is_null($customField)) {
                continue;
            }

            $type = $customField->getType();
            if (!$type->hasSplitFields()) {
                $dateSplit[$identifyKey] = $customFieldAnswererRawData;
                continue;
            }

            foreach ($customFieldAnswererRawData as $subPath => $subData) {
                $getSplitData = $this->splittingFormComponents($subData, $customFieldsIdentify);
                foreach ($getSplitData as $subKey => $subValue) {
                    $dateSplit[$subKey . '.' . $subPath] = $subValue;
                }
            }
        }
        return $dateSplit;
    }

    protected function saveWithoutPreparation(
        CustomFormAnswer $formAnswer,
        Collection $customFields,
        array $formData
    ): void {
        LogBatch::startBatch();
        $existingFieldAnswers = $this->getExistingFieldAnswers($formAnswer);
        $formRules = $formAnswer->customForm->rules;
        $answersToHandle = collect();
        $handledCustomIds = collect();
        $handledPaths = collect();

        foreach ($formData as $identifierPath => $fieldRawData) {
            //Exclude Path and Identifier
            $explodedIdentifierPath = explode('.', $identifierPath);
            $identifier = $explodedIdentifierPath[0];
            $path = null;
            if (sizeof($explodedIdentifierPath) !== 1) {
                $path = implode('.', array_slice(explode('.', $identifierPath), 1));
                $handledPaths->add($path);
            }

            /**@var $customField CustomField */
            $customField = $customFields->get($identifier);
            if (is_null($customField)) {
                continue;
            }

            //Add custom field ids ans paths in handled to identify which fields can be deleted
            $handledCustomIds->add($customField->id);

            /**@var ?CustomFieldAnswer $customFieldAnswer */
            $customFieldAnswer = $existingFieldAnswers->get($identifierPath);
            if (is_null($customFieldAnswer)) {
                $customFieldAnswer = new CustomFieldAnswer([
                    'custom_field_id' => $customField->id,
                    'custom_form_answer_id' => $formAnswer->id,
                    'path' => $path,
                ]);
            }

            $type = $customField->getType();
            $fieldAnswererData = $type->prepareToSaveAnswerData($customFieldAnswer, $fieldRawData);
            $fieldAnswererData = $this->mutateAnswerDataByRule($formRules, $customFieldAnswer, $fieldAnswererData);

            $customFieldAnswer->answer = $fieldAnswererData;
            $customFieldAnswer->setRelation('customField', $customField);
            $answersToHandle->add($customFieldAnswer);
        }
        $handledPaths = $handledPaths->filter(fn($path) => !is_null($path));
        $answersToHandle = $answersToHandle->groupBy(fn(CustomFieldAnswer $answer) => $answer->exists);

        $answersToCreate = $answersToHandle->get(false, collect())
            ->filter(function (CustomFieldAnswer $answer) {
                return !$answer->customField->getType()->isEmptyAnswer($answer, $answer->answer);
            });

        $answersToDelete = $answersToHandle->get(true, collect())
            ->filter(function (CustomFieldAnswer $answer) {
                return $answer->customField->getType()->isEmptyAnswer($answer, $answer->answer);
            });

        $answersToSave = $answersToHandle->get(true, collect())
            ->whereNotIn('id', $answersToDelete->pluck('id'))
            ->filter(fn(CustomFieldAnswer $answer) => $answer->isDirty());

        $this->handleDeleteFields($formAnswer, $answersToDelete, $handledCustomIds, $handledPaths);
        $this->handleSaveFields($answersToSave);
        $this->handleCreateFields($answersToCreate);

        $loggedFieldActivity = Activity::query()
            ->where('batch_uuid', LogBatch::getUuid())
            ->where('subject_type', CustomFieldAnswer::class)
            ->whereNot('event', 'created')
            ->exists();

        if ($loggedFieldActivity) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($formAnswer)
                ->event('update_fields')
                ->log(':causer.name updated field answares');
        }

        LogBatch::endBatch();
    }

    private function handleDeleteFields(
        CustomFormAnswer $formAnswer,
        Collection $customFieldsToDelete,
        Collection $handledCustomIds,
        Collection $handledPaths
    ): void {
        CustomFieldAnswer::query()
            ->whereIn('id', $customFieldsToDelete->pluck('id'))
            ->orWhere(fn($query) => $query
                ->where('custom_form_answer_id', $formAnswer->id)
                ->whereIn('custom_field_id', $handledCustomIds)
                ->whereNotNull('path')
                ->whereNotIn('path', $handledPaths)
            )->delete();
    }

    private function handleSaveFields(Collection $toSave): void
    {
        if ($toSave->isEmpty()) {
            return;
        }
        $now = now()->toDateTimeString();
        $answerDataToSave = $toSave
            ->map(function (CustomFieldAnswer $answer) use ($now) {
                $attributes = $this->getFieldAttributesToSave($answer);

                $attributes['updated_at'] = $now;

                unset($attributes['created_at']);
                return $attributes;
            })
            ->toArray();

        CustomFieldAnswer::upsert($answerDataToSave, ['id'], (app(CustomFieldAnswer::class))->getFillable());
    }

    private function handleCreateFields(Collection $toCreate): void
    {
        if ($toCreate->isEmpty()) {
            return;
        }
        $now = now()->toDateTimeString();
        $answerDataToSave = $toCreate
            ->map(function (CustomFieldAnswer $answer) use ($now) {
                $attributes = $this->getFieldAttributesToSave($answer);

                $attributes['updated_at'] = $now;
                $attributes['created_at'] = $now;

                return $attributes;
            })
            ->toArray();

        CustomFieldAnswer::insert($answerDataToSave);
    }

    private function getExistingFieldAnswers(CustomFormAnswer $formAnswer): Collection
    {
        return $formAnswer
            ->customFieldAnswers()
            ->with('customField')
            ->with('customField.generalField')
            ->get()
            ->mapWithKeys(function (CustomFieldAnswer $answer) {
                $path = $answer->customField->identifier . (is_null($answer->path) ? '' : '.' . $answer->path);
                return [$path => $answer];
            });
    }

    private function mutateAnswerDataByRule(
        \Illuminate\Database\Eloquent\Collection $formRules,
        ?CustomFieldAnswer $customFieldAnswer,
        mixed $fieldAnswererData
    ): mixed {
        foreach ($formRules as $rule) {
            /**@var Rule $rule */
            $fieldAnswererData = $rule->handle(
                ['action' => 'save_answer', 'custom_field_answer' => $customFieldAnswer],
                $fieldAnswererData
            );
        }
        return $fieldAnswererData;
    }

    private function prepareFormComponents(Collection $customFieldsKeyByIdentifier, Form $form, string $path): void
    {
        //ToDo That is slow (Extreme Slow) (getFlatFields)
        $components = collect($form->getFlatFields(false, true))
            ->filter(fn(Field $component, string $key) => str_starts_with($key, $path));

        foreach ($customFieldsKeyByIdentifier as $identifier => $customField) {
            $fieldComponents = $components->filter(function (Field $component, string $key) use ($identifier) {
                return str_contains($key, $identifier);
            });
            foreach ($fieldComponents as $fieldComponent) {
                /**@var CustomField $customField */
                $customField->getType()->updateAnswerFormComponentOnSave($fieldComponent, $customField, $form,
                    $components);
            }
        }
    }
}
