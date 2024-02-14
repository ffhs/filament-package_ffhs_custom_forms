<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

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
        $customFields = $form->cachedFields();

        $fieldVariations = self::getFormFieldVariations($customFields, $variation);
        $render= self::getFormRender($viewMode,$form);
        $customFormSchema = self::render(0,$fieldVariations,$customFields,$render)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function getFormRender(string $viewMode,CustomForm $form): Closure {
        return function(CustomFieldType $type, CustomFieldVariation $variation, array $parameter) use ($form, $viewMode) {
            $variation->customField = $form->cachedField($variation->custom_field_id);
            return $type->getFormComponent($variation,$form,$viewMode, $parameter);
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
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->cachedFields();
        $fieldVariations = self::getInfoListVariations($formAnswer, $customFields);
        $fieldAnswers = $formAnswer->cachedAnswers();

        $render= self::getInfolistRender($viewMode,$form, $fieldAnswers);
        $customViewSchema = self::render(0,$fieldVariations,$customFields,$render)[0];

        return  [
            \Filament\Infolists\Components\Group::make($customViewSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function getInfolistRender(string $viewMode,CustomForm $form, Collection $fieldAnswers): Closure {
        return function (CustomFieldType $type, CustomFieldVariation $variation, array $parameter) use (
            $form,
            $viewMode, $fieldAnswers
        ) {
            $answer = $fieldAnswers->firstWhere("custom_field_variation_id", $variation->id);
            if (is_null($answer)) {
                $answer = new CustomFieldAnswer();
                $answer->answer = null;
                $answer->custom_field_variation_id = $variation->id;
            }

            $variation->customField = $form->cachedField($variation->custom_field_id);
            $answer->customFieldVariation = $variation;

            return $type->getInfolistComponent($answer, $form, $viewMode, $parameter);
        };
    }


    public static function getInfoListVariations(CustomFormAnswer $formAnswer, Collection|array $customFields, Model|int|null $variationRaw = null): Collection {
        $customForm = CustomForm::cached($formAnswer->custom_form_id);
        $customFieldVariations = $customForm
            ->cachedFields()
            ->map(fn(CustomField$customField) => $customField->customFieldVariations)
            ->flatten(1);
        $fieldAnswers = $formAnswer->cachedAnswers();


        $fieldVariations = $fieldAnswers
            ->map(fn(CustomFieldAnswer $answer) => $customFieldVariations->firstWhere("id", $answer->custom_field_variation_id));

        //Find Variation
        $variation = $variationRaw;
        if (is_null($variationRaw)&& $customForm->getFormConfiguration()::hasVariations()) {
            $variation = $fieldVariations
                ->filter(fn(CustomFieldVariation $variation) => !is_null($variation->variation_id))->first();
            if (!is_null($variation)) $variation = $variation->variation_id;
        }

        if (is_null($variation) || !$customForm->getFormConfiguration()::hasVariations())
            $fieldVariationsAddon = $customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariationsAddon = $customFields->map(fn(CustomField $field) => $field->getVariation($variation));

        $fieldVariationsAddon = $fieldVariationsAddon->filter(fn(CustomFieldVariation $variation) => !$fieldVariations->contains($variation->id));
        $fieldVariationsAddon->each(fn(CustomFieldVariation $variation) => $fieldVariations->add($variation));
        return $fieldVariations;
    }

    public static function render(int $indexOffset, Collection $fieldVariations, Collection &$customFields, Closure &$render) {
        $customFormSchema = [];

        $preparedFieldsVariations = collect(
            array_combine(
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $customFields->firstWhere("id",$variation->custom_field_id)->form_position)->toArray(),
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $variation->id)->toArray()
            )
        );


        for($index = $indexOffset+1; $index<= $fieldVariations->count()+$indexOffset; $index++){

            if(empty($preparedFieldsVariations[$index]))continue;

            /** @var CustomFieldVariation $fieldVariation*/
            $fieldVariation = $fieldVariations->firstWhere("id",$preparedFieldsVariations[$index]);



            /** @var CustomField $customField*/
            $customField = $customFields->firstWhere("id",$fieldVariation->custom_field_id);
            if(!($customField->getType() instanceof CustomLayoutType)){
                if(!$fieldVariation->is_active) continue;
                $customFormSchema[] = $render($customField->getType(),$fieldVariation, []);
                continue;
            }

            $endLocation = $customField->layout_end_position;

            //Setup Render Data
            $fieldVariationRenderData = [];
            for($formPositionSubForm = $customField->form_position+1; $formPositionSubForm <= $endLocation; $formPositionSubForm++){
                $fieldVariationRenderData[] =  $fieldVariations->firstWhere("id",$preparedFieldsVariations[$formPositionSubForm]);
            }
            $fieldVariationRenderData = collect($fieldVariationRenderData);


            //Render Schema Input
            $renderedOutput = self::render($index, $fieldVariationRenderData,$customFields,$render);
            //Get Layout Schema
            $parameters = [
                "fieldVariationData" => $fieldVariationRenderData,
                "rendered"=> $renderedOutput[0],
            ]; //ToDo Optimize

            //Set Index
            $index= $renderedOutput[1]-1;

            if(!$fieldVariation->is_active) continue;
            $customFormSchema[] = $render($customField->getType(),$fieldVariation, $parameters);

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
