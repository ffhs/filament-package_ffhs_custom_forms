<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\DataManagment;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;

trait HasCustomFormSaveDataManagement
{

    public static function save(CustomFormAnswer $formAnswer, Form $form, string|null $path = null): void
    {
        // $path is then path to the customFormData in the formData
        $customForm = $formAnswer->customForm;
        // Mapping and combining custom fields
        $customFieldsIdentify = self::mapFields(
            $customForm->customFields,
            fn(CustomField $customField) => $customField->identifier
        );


        self::prepareFormComponents($customFieldsIdentify, $form);

        //Update form data after modifying components
        $formData = self::getFormData($form, $path);


        $formData = self::splittingFormComponents($formData, $customFieldsIdentify);


        // Mapping and combining field answers

        self::saveWithoutPreparation($formData, $customFieldsIdentify, $formAnswer);

        CustomFieldAnswer::clearModelCache($formAnswer->customFieldAnswers->pluck("id")->toArray());
        $formAnswer->cachedClear("customFieldAnswers");
    }

    public static function mapFields($fields, $keyCallback, $filterCallback = null): array
    {
        if ($filterCallback) {
            $fields = $fields->filter($filterCallback);
        }

        $keys = $fields->map($keyCallback)->toArray();
        $fieldsArray = [];
        $fields->each(function ($model) use (&$fieldsArray) {
            $fieldsArray[] = $model;
        });
        return array_combine($keys, $fieldsArray);
    }

    public static function prepareFormComponents(array $customFieldsIdentify, Form $form): void
    {
        $components = collect($form->getFlatComponents()); //ToDo That is slow (Extreme Slow)

        foreach ($customFieldsIdentify as $identifyKey => $customField) {
            /**@var CustomField $customField */
            $fieldComponents = $components->filter(
                fn(Component $component) => !is_null($component->getKey()) && str_contains(
                        $component->getKey(),
                        $identifyKey
                    )
            );
            foreach ($fieldComponents as $fieldComponent) {
                if (is_null($fieldComponent)) continue;
                $customField->getType()->updateFormComponentOnSave($fieldComponent, $customField, $form, $components);
            }
        }
    }

    protected static function getFormData(Form $form, ?string $path): array
    {
        $data = $form->getRawState();

        if (is_null($path)) return $data;

        $pathFragments = explode('.', $path);
        foreach ($pathFragments as $pathFragment) {
            $data = $data[$pathFragment];
        }

        return $data;
    }

    public static function splittingFormComponents(
        array $formData,
        array &$customFieldsIdentify,
        string $parentPath = ""
    ): array {
        $dateSplitted = [];
        foreach ($formData as $identifyKey => $customFieldAnswererRawData) {
            /**@var CustomField $customField */
            /**@var CustomFieldType $type */
            $customField = $customFieldsIdentify[$identifyKey] ?? null;

            if (null === $customField) continue;

            $type = $customField->getType();
            $path = empty($parentPath) ? $identifyKey : $identifyKey . "." . $parentPath;

            if (!$type->hasSplitFields()) {
                $dateSplitted[$path] = $customFieldAnswererRawData;
                continue;
            }

            foreach ($customFieldAnswererRawData as $subPath => $dateSplit) {
                $newParentPath = empty($parentPath) ? $subPath : $parentPath . "." . $subPath;
                $getSplittedData = static::splittingFormComponents($dateSplit, $customFieldsIdentify, $newParentPath);
                $dateSplitted = array_merge($dateSplitted, $getSplittedData);
            }
        }
        return $dateSplitted;
    }


    //ToDo split method and reduce queries
    public static function saveWithoutPreparation(
        array $formData,
        array $customFieldsIdentify,
        CustomFormAnswer $formAnswer
    ): void {
        LogBatch::startBatch();
        $handledCustomIds = [];
        $handledPaths = [];

        $sxisitingFieldAnsware = self::mapFields(
            $formAnswer->customFieldAnswers()->get(),
            fn(CustomFieldAnswer $answer) => $answer->customField->identifier . (is_null(
                    $answer->path
                ) ? '' : '.' . $answer->path)
        );

        foreach ($formData as $identifierPath => $fieldRawData) {
            //Exclude Path and Identifier
            $identifier = explode('.', $identifierPath)[0];
            $path = implode('.', array_slice(explode('.', $identifierPath), 1)) ?? null;
            $path = empty($path) ? null : $path;

            if (!array_key_exists($identifier, $customFieldsIdentify)) continue;//$fieldData = null

            /**@var $customField CustomField */
            $customField = $customFieldsIdentify[$identifier];
            $type = $customField->getType();


            // Which Fields are active and used
            $handledCustomIds[] = $customField->id;
            if (!is_null($path)) $handledPaths[] = $path;


            /**@var null|CustomFieldAnswer $customFieldAnswer */
            if (!empty($sxisitingFieldAnsware[$identifierPath])) {
                $customFieldAnswer = $sxisitingFieldAnsware[$identifierPath];
            } else {
                $customFieldAnswer = new CustomFieldAnswer([
                    "custom_field_id" => $customField->id,
                    "custom_form_answer_id" => $formAnswer->id,
                    "path" => $path,
                ]);
            }

            $fieldAnswererData = $customField->getType()->prepareSaveFieldData($customFieldAnswer, $fieldRawData);
            if ($type->isEmptyAnswerer($customFieldAnswer, $fieldAnswererData)) {
                if ($customFieldAnswer->exists) $customFieldAnswer->delete();
                $type->afterAnswerFieldSave($customFieldAnswer, $fieldRawData, $formData);
                continue;
            }


            $formRules = $customField->customForm->rules;
            foreach ($formRules as $rule) {
                /**@var Rule $rule */
                $fieldAnswererData = $rule->handle(
                    ['action' => "save_answerer", 'custom_field_answer' => $customFieldAnswer],
                    $fieldAnswererData
                );
            }

            $customFieldAnswer->answer = $fieldAnswererData;
            if (!$customFieldAnswer->exists || $customFieldAnswer->isDirty()) $customFieldAnswer->save();
            $type->afterAnswerFieldSave($customFieldAnswer, $fieldRawData, $formData);
        }


        //Delete not used Answares

        CustomFieldAnswer::query()
            ->where("custom_form_answer_id", $formAnswer->id)
            ->whereIn("custom_field_id", $handledCustomIds)
            ->whereNotNull("path")
            ->whereNotIn("path", $handledPaths)
            ->delete();


        $logedFieldActivity = Activity::query()
            ->where('batch_uuid', LogBatch::getUuid())
            ->where('subject_type', CustomFieldAnswer::class)
            ->whereNot('event', 'created')
            ->exists();

        if ($logedFieldActivity) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($formAnswer)
                ->event('update_fields')
                ->log(':causer.name updated field answares');
        }

        LogBatch::endBatch();
    }


}
