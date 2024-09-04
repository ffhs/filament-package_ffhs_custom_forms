<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;

class CustomFormSaveHelper {

    public static function save(CustomFormAnswer $formAnswerer, Form $form, string|null $path = null) :void{
        // $path is then path to the customFormData in the formData
        $customForm = $formAnswerer->customForm;
        // Mapping and combining custom fields
        $customFieldsIdentify = self::mapFields(
            $customForm->customFields,
            fn(CustomField $customField) => $customField->identifier
        );

        self::prepareFormComponents($customFieldsIdentify, $form);

        //Update form data after modifying components
        $formData = self::getFormData($form, $path);


        // Mapping and combining field answers
        $fieldAnswersIdentify = self::mapFields(
            $formAnswerer->customFieldAnswers()->get(),
            fn(CustomFieldAnswer $answer) => $answer->customField->identifier
        );

        self::saveWithoutPreparation($formData, $customFieldsIdentify, $fieldAnswersIdentify, $formAnswerer);
    }

    public static function saveWithoutPreparation(array $formData, array $customFieldsIdentify, array $fieldAnswersIdentify,
        CustomFormAnswer $formAnswerer): void {
        foreach ($customFieldsIdentify as $key => $customField) {
            /**@var CustomField $customField */

            if (!array_key_exists($key,$formData)) continue;//$fieldData = null
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

            $formRules = $customField->customForm->rules;
            foreach ($formRules as $rule) {
                /**@var Rule $rule */
                $fieldAnswererData = $rule->handle(['action' => "save_answerer", 'custom_field_answer' => $customFieldAnswer], $fieldAnswererData);
            }

            $customFieldAnswer->answer = $fieldAnswererData;

            if (!$customFieldAnswer->exists || $customFieldAnswer->isDirty()) $customFieldAnswer->save();

            $type->afterAnswerFieldSave($customFieldAnswer, $fieldData, $formData);
        }
    }


    public static function mapFields($fields, $keyCallback, $filterCallback = null) : array {
        if ($filterCallback)
            $fields = $fields->filter($filterCallback);

        $keys = $fields->map($keyCallback)->toArray();
        $fieldsArray = [];
        $fields->each(function($model) use (&$fieldsArray) {
            $fieldsArray[] = $model;
        });
        return array_combine($keys, $fieldsArray);
    }


    public static function getFormData(Form $form, ?string $path): array {
        $data = $form->getRawState();

        if (is_null($path)) return $data;

        $pathFragments = explode('.', $path);
        foreach ($pathFragments as $pathFragment) $data = $data[$pathFragment];

        return $data;
    }

    public static function prepareFormComponents(array $customFieldsIdentify, Form $form): void {
        $components = collect($form->getFlatComponents()); //ToDo That is sloww (Extream Sloww)

        foreach ($customFieldsIdentify as $identifyKey => $customField){
            /**@var CustomField $customField*/
            $fieldComponent = $components->first(fn(Component $component) => !is_null($component->getKey()) && str_contains($component->getKey(),$identifyKey));
            if(is_null($fieldComponent)) continue;
            $customField->getType()->updateFormComponentOnSave($fieldComponent, $customField, $form, $components);
        }
    }
}
