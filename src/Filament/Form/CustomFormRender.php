<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode, null|int|Model $variation = null):array{
        $customFields = CustomField::query()->where("custom_form_id",$form->id)->with("customFieldVariations.customField","customFieldVariations")->get();

        $fieldVariations = self::getFormFieldVariations($customFields, $variation);
        $render= self::getFormRender($viewMode);
        $customFormSchema = self::render(0,$fieldVariations,$customFields,$render)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function getFormRender($viewMode) {
        return function(CustomFieldType $type, CustomFieldVariation $variation, array $parameter) use ($viewMode) {
            return $type->getFormComponent($variation,$viewMode, $parameter);
        };
    }

    public static function getFormFieldVariations(Collection $customFields,null|int|Model $variation = null) :Collection{
        if(is_null($variation))
            $fieldVariations = $customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariations = $customFields->map(fn(CustomField $field) => $field->getVariation($variation));
        return $fieldVariations;
    }


    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $customFields = CustomField::query()->where("custom_form_id",$formAnswer->customForm->id)->with("customFieldVariations.customField","customFieldVariations")->get();
        $fieldVariations = self::getInfoListVariations($formAnswer, $customFields);

        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= self::getInfolistRender($viewMode, $fieldAnswers);
        $customViewSchema = self::render(0,$fieldVariations,$customFields,$render)[0];

        return  [
            \Filament\Infolists\Components\Group::make($customViewSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    private static function getInfolistRender(string $viewMode, Collection $fieldAnswers): Closure {
        return function (CustomFieldType $type, CustomFieldVariation $variation, array $parameter) use (
            $viewMode, $fieldAnswers
        ) {
            $answer = $fieldAnswers->firstWhere("custom_field_variation_id", $variation->id);
            if (is_null($answer)) {
                $answer = new CustomFieldAnswer();
                $answer->answer = null;
                $answer->custom_field_variation_id = $variation->id;
            }
            return $type->getInfolistComponent($answer, $viewMode, $parameter);
        };
    }


    private static function getInfoListVariations(CustomFormAnswer $formAnswer, Collection|array $customFields): Collection {
        $fieldVariations = $formAnswer->customFieldAnswers->map(fn(CustomFieldAnswer $answer) => $answer->customFieldVariation);

        $variation = null;
        if ($formAnswer->customForm->getFormConfiguration()::hasVariations()) {
            $variation = $formAnswer->customFieldAnswers
                ->map(fn(CustomFieldAnswer $answer) => $answer->customFieldVariation)
                ->filter(fn(CustomFieldVariation $variation) => is_null($variation->variation_id))->first();
            if (!is_null($variation)) $variation = $variation->id;
        }

        if (is_null($variation))
            $fieldVariationsAddon = $customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariationsAddon = $customFields->map(fn(CustomField $field) => $field->getVariation($variation));

        $fieldVariationsAddon = $fieldVariationsAddon->filter(fn(CustomFieldVariation $variation) => !$fieldVariations->contains($variation->id));
        $fieldVariationsAddon->each(fn(CustomFieldVariation $variation) => $fieldVariations->add($variation));
        return $fieldVariations;
    }

    public static function render(int $indexOffset, Collection $fieldVariations, Collection &$customFields, Closure &$render) {
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
                $customFormSchema[] = $render($customField->getType(),$fieldVariation, []);
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
            $renderedOutput = self::render($index, $fieldVariationRenderData,$customFields,$render);
            //Get Layout Schema
            $parameters = [
                "fieldVariationData" => $fieldVariationRenderData,
                "rendered"=> $renderedOutput[0],
            ];
            $customFormSchema[] = $render($customField->getType(),$fieldVariation, $parameters);
            //Set Index
            $index= $renderedOutput[1]-1;

        }
        return [$customFormSchema,$index];
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



}
