<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

final class TemplateFieldType extends CustomFieldType
{

    public static function getFieldIdentifier(): string {
        return "template";
    }

    public function viewModes(): array {
        return [
          "default"=> TemplateTypeView::class,
        ];
    }

    public function icon(): string {
        return "carbon-copy-file";
    }

    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData): void {
        $templateId = $field->customField->template_id;
        $template = CustomForm::cached($templateId);
        $formAnswerer = $field->customFormAnswer;

        $customFieldAnswers = $formAnswerer->customFieldAnswers;
        $keys = $customFieldAnswers
            ->filter(fn(CustomFieldAnswer $answer)=>$answer->customField->custom_form_id = $templateId)
            ->map(fn(CustomFieldAnswer $answer)=> $answer->customField->getInheritState()["identify_key"])
            ->toArray();
        $customFieldAnswersArray = [];
        $customFieldAnswers->each(function($model) use (&$customFieldAnswersArray) {$customFieldAnswersArray[] = $model;});
        $fieldAnswersIdentify = array_combine($keys, $customFieldAnswersArray);

        $customFields = $template->cachedFields();
        $keys = $customFields->map(fn(CustomField $customField)=> $customField->getInheritState()["identify_key"])->toArray();
        $customFieldArray = [];
        $customFields->each(function($model) use (&$customFieldArray) {$customFieldArray[] = $model;});
        $customFieldsIdentify = array_combine($keys, $customFieldArray);


        CustomFormRender::saveHelperWithoutPreparation($formData, $customFieldsIdentify, $fieldAnswersIdentify, $formAnswerer);

    }


}
