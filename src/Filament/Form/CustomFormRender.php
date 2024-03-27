<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Group;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode):array{
        $customFields = $form->cachedFields();

        $render= self::getFormRender($viewMode,$form);
        $customFormSchema = self::render(0,$customFields,$render)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
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

    public static function getFormRender(string $viewMode,CustomForm $form): Closure {
        return function(CustomField $customField, array $parameter) use ($viewMode, $form) {

            //Render
            $component = $customField->getType()->getFormComponent($customField, $form, $viewMode, $parameter);
            $component->live();

            return $component;
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
            $parameters = [];

            if(!$customField->is_active) continue;
            if(($customField->getType() instanceof CustomLayoutType)){

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
            }

            $rules = $customField->fieldRules;

            //Rule before render
            $rules->each(function(FieldRule $rule) use (&$customField) {
                $rule->getRuleType()->beforeRender($customField,$rule);
            });

            //Parameter anchor mutation
            $rules->each(function(FieldRule $rule) use (&$parameters, &$customField) {
                $parameters =  $rule->getAnchorType()->mutateRenderParameter($parameters,$customField,$rule);
            });

            //Parameter rule mutation
            $rules->each(function(FieldRule $rule) use (&$parameters, &$customField) {
                $parameters =  $rule->getRuleType()->mutateRenderParameter($parameters,$customField,$rule);
            });

            //Render
            $renderedComponent = $render($customField, $parameters);

            //Rule after Render
            $rules->each(function(FieldRule $rule) use (&$customField, &$renderedComponent) {
                $renderedComponent = $rule->getRuleType()->afterRender($renderedComponent,$customField,$rule);
            });
            $customFormSchema[] = $renderedComponent;

        }
        return [$customFormSchema,$index];
    }


    public static function saveHelper(CustomFormAnswer $formAnswerer, array $formData) :void{
        $customForm = CustomForm::cached($formAnswerer->custom_form_id);

        $customFieldAnswers = $formAnswerer->customFieldAnswers;
        $keys = $customFieldAnswers
            ->map(fn(CustomFieldAnswer $answer)=> $answer->customField->getInheritState()["identify_key"])
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
            if(empty($customFieldsIdentify[$key])) continue;


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
                    "custom_field_id" => $customField->id,
                    "custom_form_answer_id" => $formAnswerer->id,
                ]);
            else $customFieldAnswer = $fieldAnswersIdentify[$key];

            $fieldRules  = $customFieldAnswer->customField->fieldRules;
            foreach ($fieldRules as $rule){
                /**@var FieldRule $rule */
                $fieldAnswererData = $rule->getRuleType()->mutateSaveAnswerData($fieldAnswererData,$rule, $customFieldAnswer);
            }

            $customFieldAnswer->answer = $fieldAnswererData;

            foreach ($fieldRules as $rule){
                /**@var FieldRule $rule */
                $rule->getRuleType()->afterAnswerSave($rule, $customFieldAnswer);
            }


            if(!$customFieldAnswer->exists|| $customFieldAnswer->isDirty())$customFieldAnswer->save();
        }

    }



    public static function loadHelper(CustomFormAnswer $answerer):array {
        $data = [];
        $form = CustomForm::cached($answerer->custom_form_id);

        $customFields = $form->cachedFields();

        foreach($answerer->customFieldAnswers as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            /**@var CustomField $customField*/
            $customField = $customFields->where("id", $fieldAnswer->custom_field_id)->first();
            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);

            $fieldRules  = $customField->fieldRules;
            foreach ($fieldRules as $rule){
                /**@var FieldRule $rule */
                $fieldData = $rule->getRuleType()->mutateLoadAnswerData($fieldData,$rule, $fieldAnswer);
            }

            $data[$customField->getInheritState()["identify_key"]] = $fieldData;
        }
        return $data;
    }



}
