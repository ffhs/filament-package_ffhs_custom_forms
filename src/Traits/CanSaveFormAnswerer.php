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

trait CanSaveFormAnswerer
{
    public function saveFormAnswerer(CustomFormAnswer $formAnswer, Form $form, array $data, string $pathRaw = ''): void
    {
        $customForm = $formAnswer->customForm;
        $path = 'data.' . $pathRaw;

        // Mapping and combining custom fields
        $customFieldsIdentify = $customForm
            ->customFields
            ->mapWithKeys(function (CustomField $customField) {
                return [$customField->identifier => $customField];
            });

        //Update form data after modifying components
        $this->prepareFormComponents($customFieldsIdentify, $form, $path);

        // Mapping and combining field answers
        $data = $this->splittingFormComponents($data, $customFieldsIdentify);

        try {
            DB::beginTransaction();
            $this->saveWithoutPreparation($formAnswer, $customFieldsIdentify, $data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        CustomFieldAnswer::clearModelCache($formAnswer->customFieldAnswers->pluck("id")->toArray());
        $formAnswer->cachedClear("customFieldAnswers");
    }

    protected function splittingFormComponents(
        array $formData,
        Collection $customFieldsIdentify,
        string $parentPath = ""
    ): array {
        $dateSplit = [];
        foreach ($formData as $identifyKey => $customFieldAnswererRawData) {
            /**@var CustomField $customField */
            $customField = $customFieldsIdentify->get($identifyKey);
            if (is_null($customField)) {
                continue;
            }

            $type = $customField->getType();
            $path = empty($parentPath) ? $identifyKey : $identifyKey . '.' . $parentPath;

            if (!$type->hasSplitFields()) {
                $dateSplit[$path] = $customFieldAnswererRawData;
                continue;
            }

            foreach ($customFieldAnswererRawData as $subPath => $dateSplit) {
                $newParentPath = empty($parentPath) ? $subPath : $parentPath . '.' . $subPath;
                $getSplitData = $this->splittingFormComponents($dateSplit, $customFieldsIdentify, $newParentPath);
                $dateSplit = [...$dateSplit, ...$getSplitData];
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
            $identifier = explode('.', $identifierPath)[0];
            $path = implode('.', array_slice(explode('.', $identifierPath), 1));
            $path = empty($path) ? null : $path;

            /**@var $customField CustomField */
            $customField = $customFields->get($identifier);
            if (is_null($customField)) {
                continue;
            }

            //Add custom field ids ans paths in handled to identify which fields can be deleted
            $handledCustomIds->add($customField->id);
            $handledPaths->add($path);

            /**@var ?CustomFieldAnswer $customFieldAnswer */
            $customFieldAnswer = $existingFieldAnswers->get($identifierPath);
            if (is_null($customFieldAnswer)) {
                $customFieldAnswer = new CustomFieldAnswer([
                    "custom_field_id" => $customField->id,
                    "custom_form_answer_id" => $formAnswer->id,
                    "path" => $path,
                ]);
            }

            $type = $customField->getType();
            $fieldAnswererData = $type->prepareSaveFieldData($customFieldAnswer, $fieldRawData);


            $fieldAnswererData = $this->mutateAnswerDataByRule($formRules, $customFieldAnswer, $fieldAnswererData);

            $customFieldAnswer->answer = $fieldAnswererData;
            $answersToHandle->add($customFieldAnswer);
        }

        $this->handleDeleteFields($formAnswer, $handledCustomIds, $handledPaths);
        $answersToHandle = $answersToHandle->groupBy(fn(CustomFieldAnswer $answer) => $answer->exists);
        $answersToCreate = $answersToHandle->get(false)
            ?->filter(fn(CustomFieldAnswer $answer) => !empty($answer->answer));
        $answersToSave = $answersToHandle->get(true)
            ?->filter(fn(CustomFieldAnswer $answer) => $answer->isDirty());

        if (!is_null($answersToCreate)) {
            $answerDataToCreate = $answersToCreate
                ->map(fn(CustomFieldAnswer $answer) => $answer->attributesToArray())
                ->toArray();

            CustomFieldAnswer::insert($answerDataToCreate);
        }

        if (!is_null($answersToSave)) {
            $answerDataToSave = $answersToSave
                ->map(fn(CustomFieldAnswer $answer) => $answer->attributesToArray())
                ->toArray();

            CustomFieldAnswer::upsert($answerDataToSave, ['id']);
        }


        $updatedFields = CustomFormAnswer::whereIn('id', $answerDataToSave->pluck("id"))->get();

        $existingFieldAnswers->keyBy('id');
        $updatedFields->each(function (CustomFieldAnswer $answer) use ($formData, $existingFieldAnswers): void {
            /**@var CustomField $customField */
            $customField = $existingFieldAnswers->get($answer->id);
            $type = $customField->getType();
            $answer->setRelation('customField', $customField);
            $type->afterAnswerFieldSave($answer, $formData);
        });
#
        //ToDo
//        if ($type->isEmptyAnswerer($customFieldAnswer, $fieldAnswererData)) {
//            if ($customFieldAnswer->exists) {
//                $customFieldAnswer->delete();
//            }
//            $type->afterAnswerFieldSave($customFieldAnswer, $fieldRawData, $formData);
//            continue;
//        }


        LogBatch::endBatch();
    }

    private function handleDeleteFields(
        CustomFormAnswer $formAnswer,
        Collection $handledCustomIds,
        Collection $handledPaths
    ): void {
        CustomFieldAnswer::query()
            ->where("custom_form_answer_id", $formAnswer->id)
            ->whereIn("custom_field_id", $handledCustomIds)
            ->whereNotNull("path")
            ->whereNotIn("path", $handledPaths)
            ->delete();
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
                ['action' => "save_answerer", 'custom_field_answer' => $customFieldAnswer],
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
                $customField->getType()->updateFormComponentOnSave($fieldComponent, $customField, $form, $components);
            }
        }
    }
}
