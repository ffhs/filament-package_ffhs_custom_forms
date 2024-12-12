<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TemplatesType;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\TypeActions\default\DefaultTemplateDissolveAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\html\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

final class TemplateFieldType extends CustomFieldType
{

    public static function identifier(): string {
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

    public function isFullSizeField(): bool
    {
        return true;
    }


    /*  public function repeaterFunctions(): array {
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
      }*/
    public function mutateCustomFieldDataOnSave(CustomField $field, array $data): array {
        unset($data['options']);
        return  $data;
    }




    public function getEditorActions(string $key, array $fieldState): array {
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            DefaultTemplateDissolveAction::make('dissolve-template-' . $key),
            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }
    public function getEditorFieldTitle(array $fieldData): string {

        $template = CustomForm::cached($fieldData['template_id']);

        return "<div>". new HtmlBadge('Template', Color::rgb("rgb(34, 135, 0)"))."</div>" .
            '<p style="margin-left: 70px; margin-top: -20px">'. $template->short_title.'</p>'; //ToDo Improve
    }

    public function hasEditorNameElement(array $fielData): bool {
        return false;
    }




    public function afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData): void {
        $templateId = $field->customField->template_id;
        $formAnswerer = $field->customFormAnswer;
        $template = CustomForm::cached($templateId);

        // Mapping and combining filtered field answers
        $fieldAnswersIdentify = CustomFormSaveHelper::mapFields(
            $formAnswerer->customFieldAnswers,
            fn(CustomFieldAnswer $answer) => $answer->customField->identifier,
            fn(CustomFieldAnswer $answer) => $answer->customField->custom_form_id == $templateId
        );

        // Mapping and combining custom fields
        $customFieldsIdentify = CustomFormSaveHelper::mapFields(
            $template->customFields,
            fn(CustomField $customField) => $customField->identifier
        );

        CustomFormSaveHelper::saveWithoutPreparation($formData, $customFieldsIdentify, $fieldAnswersIdentify, $formAnswerer); //Check with splited
    }


    //ToDo Reimplement

    public function afterDeleteField(CustomField $field):void {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;
        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use ($formFields, $field, $templateFields) {
            $formAnswer->customFieldAnswers
                ->whereIn("custom_field_id",$templateFields->pluck("id"))
                ->each($this->getFieldTransferClosure($formFields,  $templateFields));
        });
    }

    function getFieldTransferClosure(Collection $newFields, Collection $originalFields): Closure {
        return function (CustomFieldAnswer $fieldAnswer) use ($newFields, $originalFields):void {
            /**@var CustomField $oldField */
            $oldField = $originalFields->where("id", $fieldAnswer->custom_field_id)->first();
            if(is_null($oldField)) return;


            $identifier = $oldField->identifier;
            $newField = $newFields
                ->filter(fn(CustomField $customField) => $customField->identifier==$identifier)
                ->first();

            if (is_null($newField)) return;
            $fieldAnswer->custom_field_id = $newField->id;
            $fieldAnswer->save();
        };
    }

    public function afterSaveField(CustomField $field, array $data): void {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;

        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use ($formFields, $field, $templateFields) {
            $templateIdentifiers = $templateFields->pluck("identifier");
            $formFieldIds = $formFields->whereIn("identifier",$templateIdentifiers)->pluck("id");
            $formAnswer->customFieldAnswers
                ->whereIn("custom_field_id",$formFieldIds)
                ->each($this->getFieldTransferClosure($templateFields, $formFields));
        });
    }




}
