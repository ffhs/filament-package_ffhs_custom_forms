<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CustomFormRenderForm
{
    public static function generateFormSchema(CustomForm $form, string $viewMode, null|int|Model $variation = null):array{

        $customFields = CustomField::query()->where("custom_form_id",$form->id)->with("customFieldVariations.customField","customFieldVariations")->get();

        /** @var Collection $fieldVariations*/
        if(is_null($variation))
            $fieldVariations = $customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariations = $customFields->map(fn(CustomField $field) => $field->getVariation($variation));


        $customFormSchema = self::renderForm(0,$fieldVariations,$viewMode,$customFields)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function saveHelper(CustomFormAnswer $fieldAnswerer, array $formData, Model|int $variation) :void{

        $customFieldAnswers = $fieldAnswerer->customFieldAnswers;
        $keys = $customFieldAnswers->map(fn(CustomFieldAnswer $answer)=> $answer->customFieldVariation->customField->getInheritState()["identify_key"])->toArray();
        $customFieldAnswersArray = [];
        $customFieldAnswers->each(function($model) use (&$customFieldAnswersArray) {$customFieldAnswersArray[] = $model;});
        $fieldAnswersIdentify = array_combine($keys, $customFieldAnswersArray);

        $customFields = $fieldAnswerer->customForm->customFields;
        $keys = $customFields->map(fn(CustomField $customField)=> $customField->getInheritState()["identify_key"])->toArray();
        $customFieldArray = [];
        $customFields->each(function($model) use (&$customFieldArray) {$customFieldArray[] = $model;});
        $customFieldsIdentify = array_combine($keys, $customFieldArray);

        foreach($formData as $key => $fieldData){
            if(empty($customFieldsIdentify[$key])){
                continue;
            }

            /**@var CustomField $customField*/
            /**@var null|CustomFieldAnswer $customFieldAnswer*/
            $customField = $customFieldsIdentify[$key];
            $fieldAnswererData = $customField->getType()->prepareSaveFieldData($fieldData);
            if(empty($fieldAnswererData)) {
                if(empty($fieldAnswersIdentify[$key])) $customFieldAnswer->delete();
                continue;
            }

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
        foreach($answerer->customFieldAnswers as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            $customField =$fieldAnswer
                ->customFieldVariation
                ->customField;
            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);
            $data[$customField->getInheritState()["identify_key"]] = $fieldData;
        }
            return $data;
    }

    public static function renderForm(int $indexOffset, Collection $fieldVariations, string $viewMode, Collection $customFields) {
        $customFormSchema = [];

        $preparedFields = collect(
            array_combine(
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $customFields->firstWhere("id",$variation->custom_field_id)->form_position)->toArray(),
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $variation->id)->toArray()
            )
        );


        for($index = $indexOffset+1; $index<= $fieldVariations->count()+$indexOffset; $index++){

            if(empty($preparedFields[$index]))continue;

            /** @var CustomFieldVariation $fieldVariation*/
            $fieldVariation = $fieldVariations->firstWhere("id",$preparedFields[$index]);

            if(!$fieldVariation->is_active) continue;

            /** @var CustomField $customField*/
            $customField = $customFields->firstWhere("id",$fieldVariation->custom_field_id);
            if(!($customField->getType() instanceof CustomLayoutType)){
                $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode);
                continue;
            }

            $endLocation = $customField->layout_end_position;

            //Setup Render Data
            $fieldVariationRenderData = [];
            for($formPositionSubForm = $customField->form_position+1; $formPositionSubForm <= $endLocation; $formPositionSubForm++){
                $fieldVariationRenderData[] =  $fieldVariations->firstWhere("id",$preparedFields[$formPositionSubForm]);
            }
            $fieldVariationRenderData = collect($fieldVariationRenderData);


            //Render Schema Input
            $renderedOutput = self::renderForm($index,$fieldVariationRenderData, $viewMode,$customFields);
            //Get Layout Schema
            $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode, [
                "fieldVariationData" => $fieldVariationRenderData,
                "rendered"=> $renderedOutput[0],
            ]);
            //Set Index
            $index= $renderedOutput[1]-1;

        }
        return [$customFormSchema,$index];
    }

    //Todo Make InfoList Render

    //Todo Make save and load help for answars

}
