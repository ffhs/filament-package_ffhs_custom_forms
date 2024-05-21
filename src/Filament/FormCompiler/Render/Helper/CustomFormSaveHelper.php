<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;

class CustomFormSaveHelper {

    public static function save(CustomFormAnswer $formAnswerer, array $formData) :void{
        $customForm = CustomForm::cached($formAnswerer->custom_form_id);

        // Mapping and combining field answers
        $fieldAnswersIdentify = self::mapFields(
            $formAnswerer->cachedAnswers(),
            fn(CustomFieldAnswer $answer) => $answer->customField->getInheritState()["identify_key"]
        );

        // Mapping and combining custom fields
        $customFieldsIdentify = self::mapFields(
            $customForm->cachedFields(),
            fn(CustomField $customField) => $customField->getInheritState()["identify_key"]
        );

        self::saveWithoutPreparation($formData, $customFieldsIdentify, $fieldAnswersIdentify, $formAnswerer);
    }

    public static function saveWithoutPreparation(array $formData, array $customFieldsIdentify, array $fieldAnswersIdentify,
        CustomFormAnswer $formAnswerer): void {
        foreach ($customFieldsIdentify as $key => $customField) {
            /**@var CustomField $customField */

            if (!array_key_exists($key,$formData)) $fieldData = null;
            else $fieldData = $formData[$key];

            $type = $customField->getType();

            /**@var null|CustomFieldAnswer $customFieldAnswer */
            if (!empty($fieldAnswersIdentify[$key]))
                $customFieldAnswer = $fieldAnswersIdentify[$key];
            else{
                $customFieldAnswer = new CustomFieldAnswer([
                    "custom_field_id" => $customField->id,
                    "custom_form_answer_id" => $formAnswerer->id,
                ]);
            }

            $fieldAnswererData = $customField->getType()->prepareSaveFieldData($fieldData);
            if (empty($fieldAnswererData)) {
                if ($customFieldAnswer->exists)$customFieldAnswer->delete();
                $type->afterAnswerFieldSave($customFieldAnswer, $fieldData, $formData);
                continue;
            }

            $fieldRules = $customField->fieldRules;
            foreach ($fieldRules as $rule) {
                /**@var FieldRule $rule */
                $fieldAnswererData = $rule->getRuleType()->mutateSaveAnswerData($fieldAnswererData, $rule,
                    $customFieldAnswer);
            }

            $customFieldAnswer->answer = $fieldAnswererData;

            foreach ($fieldRules as $rule) {
                /**@var FieldRule $rule */
                $rule->getRuleType()->afterAnswerSave($rule, $customFieldAnswer);
            }

            if (!$customFieldAnswer->exists || $customFieldAnswer->isDirty()) $customFieldAnswer->save();

            $type->afterAnswerFieldSave($customFieldAnswer, $fieldData, $formData);
        }
    }


    public static function mapFields($fields, $keyCallback, $filterCallback = null) : array {
        if ($filterCallback) {
            $fields = $fields->filter($filterCallback);
        }
        $keys = $fields->map($keyCallback)->toArray();
        $fieldsArray = [];
        $fields->each(function($model) use (&$fieldsArray) {
            $fieldsArray[] = $model;
        });
        return array_combine($keys, $fieldsArray);
    }
}
