<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\EditAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\TemplateDissolveAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

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

    public function repeaterFunctions(): array {
        $original = parent::repeaterFunctions();
        unset($original[EditAction::class]);
        return array_merge($original,[
            TemplateDissolveAction::class => function (CustomForm $record,Get $get, array $state, array $arguments) {
                $item = $state[$arguments["item"]];
                return array_key_exists("template_id",$item) &&!is_null($item["template_id"]);
            },
        ]);
    }


    public function nameFormEditor(array $state):string {
        $template = CustomForm::cached($state["template_id"]);
        return $template->short_title;
    }

    public function nameBeforeIconFormEditor(array $state):string {
        return new HtmlBadge("Template", Color::rgb("rgb(34, 135, 0)"));
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

    public function afterEditFieldDelete(CustomField $field):void {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;
        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use ($formFields, $field, $templateFields) {
            $formAnswer->customFieldAnswers
                ->whereIn("custom_field_id",$templateFields->pluck("id"))
                ->each($this->getFieldTransferClosure($formFields,  $templateFields));
        });
    }

    public function afterEditFieldSave(CustomField $field, array $rawData): void {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;

        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use ($formFields, $field, $templateFields) {
            $templateIdentifiers = $templateFields->pluck("identify_key");
            $formFieldIds = $formFields->whereIn("identify_key",$templateIdentifiers)->pluck("id");
            $formAnswer->customFieldAnswers
                ->whereIn("custom_field_id",$formFieldIds)
                ->each($this->getFieldTransferClosure($templateFields, $formFields));
        });
    }


    function getFieldTransferClosure(Collection $newFields, Collection $originalFields): Closure {
        return function (CustomFieldAnswer $fieldAnswer) use ($newFields, $originalFields):void {
            /**@var CustomField $oldField */
            $oldField = $originalFields->where("id", $fieldAnswer->custom_field_id)->first();
            if(is_null($oldField)) return;


            $identifier = $oldField->getInheritState()["identify_key"];
            $newField = $newFields
                ->filter(fn(CustomField $customField) => $customField->getInheritState()["identify_key"]==$identifier)
                ->first();

            if (is_null($newField)) return;
            $fieldAnswer->custom_field_id = $newField->id;
            $fieldAnswer->save();
        };
    }


}
