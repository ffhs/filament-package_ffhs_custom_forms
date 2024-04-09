<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;

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

    public function nameFormEditor(array $state):string {
        $template = CustomForm::cached($state["template_id"]);
        return $template->short_title;
    }

    public function nameBeforeIconFormEditor(array $state):string {
        $templateBadge = new HtmlBadge("Template", Color::rgb("rgb(34, 135, 0)"));
        return $templateBadge. parent::nameFormEditor($state);
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

    public function afterEditFieldDelete(CustomField $record):void {
        $templateFields = $record->template->customFields;
        $formFields = $record->customForm->customFields;
        $record->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use ($formFields, $record, $templateFields) {
            $formAnswer->customFieldAnswers()
                ->whereIn("custom_field_id",$record->template->customFields()->select("id"))
                ->each(function(CustomFieldAnswer $fieldAnswer) use ($templateFields, $formFields) {
                    $templateField = $templateFields->where("id", $fieldAnswer->custom_field_id)->first();
                    /**@var CustomField $templateField*/
                    if(!$templateField->isGeneralField()){
                        $identifier = $templateField->identify_key;
                        $newField = $formFields->where("identify_key", $identifier)->first();
                    }
                    else{
                        $genField = $templateField->general_field_id;
                        $newField = $formFields->where("general_field_id", $genField)->first();
                    }
                    if(is_null($newField)) return;
                    $fieldAnswer->custom_field_id = $newField->id;
                    $fieldAnswer->save();
                });
        });
    }


}
