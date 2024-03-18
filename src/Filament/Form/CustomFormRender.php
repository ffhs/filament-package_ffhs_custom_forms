<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode, null|int|Model $variation = null):array{
        $customFields = $form->cachedFields();

        $render= self::getFormRender($viewMode,$form);
        $customFormSchema = self::render(0,$customFields,$render)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function getFormRender(string $viewMode,CustomForm $form): Closure {
        return fn(CustomField $customField, array $parameter) =>
            $customField->getType()->getFormComponent($customField,$form,$viewMode, $parameter);
    }


    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->cachedFields();
        $fieldAnswers = $formAnswer->cachedAnswers();

        $render= self::getInfolistRender($viewMode,$form, $fieldAnswers);
        $customViewSchema = self::render(0,$customFields,$render)[0];

        return  [
            \Filament\Infolists\Components\Group::make($customViewSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function getInfolistRender(string $viewMode,CustomForm $form, Collection $fieldAnswers): Closure {
        return function (CustomField $customField,  array $parameter) use ($form, $viewMode, $fieldAnswers) {


            /** @var CustomFormAnswer $answer*/
            $answer = $fieldAnswers->firstWhere("custom_field_id", $customField->id);
            if (is_null($answer)) {
                $answer = new CustomFieldAnswer();
                $answer->answer = null;
                $answer->custom_field_id = $customField->id;
            }


            return $customField->getType()->getInfolistComponent($answer, $form, $viewMode, $parameter);
        };
    }



    public static function render(int $indexOffset, Collection $customFields, Closure &$render): array {
        $customFormSchema = [];

        $preparedFields = [];
        $customFields->each(function(CustomField $field) use (&$preparedFields){
            $preparedFields[$field->form_position] = $field;
        });


        for($index = $indexOffset+1; $index<= $customFields->count()+$indexOffset; $index++){

            /** @var CustomField $customField*/
            $customField =  $preparedFields[$index];

            if(!$customField->is_active) continue;
            if(!($customField->getType() instanceof CustomLayoutType)){
                $customFormSchema[] = $render($customField, []);
                continue;
            }
            $endLocation = $customField->layout_end_position;

            //Setup Render Data
            $fieldRenderData = [];
            for($formPositionSubForm = $customField->form_position+1; $formPositionSubForm <= $endLocation; $formPositionSubForm++){
                $fieldRenderData[] =  $preparedFields[$formPositionSubForm];
            }
            $fieldRenderData = collect($fieldRenderData);

            //Render Schema Input
            $renderedOutput = self::render($index, $fieldRenderData, $render);
            //Get Layout Schema
            $parameters = [
                "customFieldData" => $fieldRenderData,
                "rendered"=> $renderedOutput[0],
            ]; //ToDo Optimize

            //Set Index
            $index= $renderedOutput[1]-1;

            $customFormSchema[] = $render($customField, $parameters);

        }
        return [$customFormSchema,$index];
    }


    public static function saveHelper(CustomFormAnswer $fieldAnswerer, array $formData, Model|int|null $variation) :void{
        $customForm = CustomForm::cached($fieldAnswerer->custom_form_id);

        $customFieldAnswers = $fieldAnswerer->customFieldAnswers;
        $keys = $customFieldAnswers
            ->map(function(CustomFieldAnswer $answer) use ($customForm) {
                $customFieldId = $answer->customFieldVariation->custom_field_id;
                $customField = $customForm->cachedField($customFieldId);
                return $customField->getInheritState()["identify_key"];
            })
            ->toArray();
        $customFieldAnswersArray = [];
        $customFieldAnswers->each(function($model) use (&$customFieldAnswersArray) {$customFieldAnswersArray[] = $model;});
        $fieldAnswersIdentify = array_combine($keys, $customFieldAnswersArray);

        $customFields = $customForm->cachedFields();
        $keys = $customFields->map(fn(CustomField $customField)=> $customField->getInheritState()["identify_key"])->toArray();
        $customFieldArray = [];
        $customFields->each(function($model) use (&$customFieldArray) {$customFieldArray[] = $model;});
        $customFieldsIdentify = array_combine($keys, $customFieldArray);

        foreach($formData as $key => $fieldData){
            if(empty($customFieldsIdentify[$key])){
                continue;
            }

            /**@var CustomField $customField*/
            $customField = $customFieldsIdentify[$key];
            $fieldAnswererData = $customField->getType()->prepareSaveFieldData($fieldData);
            if(empty($fieldAnswererData)) {
                if(!empty($fieldAnswersIdentify[$key])) $fieldAnswersIdentify[$key]->delete();
                continue;
            }

            /**@var null|CustomFieldAnswer $customFieldAnswer*/
            if(empty( $fieldAnswersIdentify[$key]))
                $customFieldAnswer= new CustomFieldAnswer([
                    "custom_field_variation_id" => $customField->getVariation($variation)->id,
                    "custom_form_answer_id" => $fieldAnswerer->id,
                ]);
            else $customFieldAnswer = $fieldAnswersIdentify[$key];

            $customFieldAnswer->answer = $fieldAnswererData;
            $customFieldAnswer->save();
        }

    }



    public static function loadHelper(CustomFormAnswer $answerer):array {
        $data = [];
        $form = CustomForm::cached($answerer->custom_form_id);

        $customFields = $form->cachedFields();

        foreach($answerer->customFieldAnswers as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            $customField = $customFields
                ->filter(fn(CustomField $field) =>
                    !is_null(
                        $field->customFieldVariations->firstWhere("id", $fieldAnswer->custom_field_variation_id)
                    )
                )
                ->first();
            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);
            $data[$customField->getInheritState()["identify_key"]] = $fieldData;
        }
        return $data;
    }



}
