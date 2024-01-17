<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CustomFormRenderForm
{


    public static function generateFormSchema(CustomForm $form, string $viewMode, null|int|Model $variation = null):array{

        /** @var Collection $fieldVariations*/
        if(is_null($variation))
            $fieldVariations = $form->customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariations = $form->customFields->map(fn(CustomField $field) => $field->getVariation($variation));


        $customFormSchema = self::renderForm(0,$fieldVariations,$viewMode)[0];
        Debugbar::info("   ");

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function renderForm(int $indexOffset, Collection $fieldVariations, string $viewMode) {
        $customFormSchema = [];

        $preparedFields = collect(
            array_combine(
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $variation->customField->form_position)->toArray(),
                $fieldVariations->map(fn(CustomFieldVariation $variation)=> $variation->id)->toArray()
            )
        );


        for($index = $indexOffset+1; $index<= $fieldVariations->count()+$indexOffset; $index++){

            if(empty($preparedFields[$index]))continue;

            /** @var CustomFieldVariation $fieldVariation*/
            $fieldVariation = $fieldVariations->firstWhere("id",$preparedFields[$index]);

            if(!$fieldVariation->is_active) continue;

            $customField = $fieldVariation->customField;
            if(!($customField->getType() instanceof CustomLayoutType)){
                $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode);
                continue;
            }

            $endLocation = $fieldVariation->customField->layout_end_position;

            //Setup Render Data
            $fieldVariationRenderData = [];
            for($formPositionSubForm = $customField->form_position+1; $formPositionSubForm <= $endLocation; $formPositionSubForm++){
                $fieldVariationRenderData[] =  $fieldVariations->firstWhere("id",$preparedFields[$formPositionSubForm]);
            }
            $fieldVariationRenderData = collect($fieldVariationRenderData);


            //Render Schema Input
            $renderedOutput = self::renderForm($index,$fieldVariationRenderData, $viewMode);
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
