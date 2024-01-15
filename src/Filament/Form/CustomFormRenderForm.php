<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

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

        return  [
            Group::make($customFormSchema)->columns(8),
        ];
    }


    public static function renderForm(int $indexOffset, Collection $fieldVariations, string $viewMode) {
        $customFormSchema = [];
        for($index = $indexOffset; $index<$fieldVariations->count()+$indexOffset; $index++){
            if(empty( $fieldVariations[$index]))dd($fieldVariations);
             /** @var CustomFieldVariation $fieldVariation*/
            $fieldVariation = $fieldVariations[$index];

            if(!$fieldVariation->is_active) continue;

            $customField = $fieldVariation->customField;
            if(!($customField->getType() instanceof CustomLayoutType)){
                $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode)->columnStart(1);
                continue;
            }


            $endLocation = $fieldVariation->options["end_location"];
            $fieldVariationData = $fieldVariations->slice($index+1,$endLocation-$indexOffset);
            $renderedOutput = self::renderForm($indexOffset+$index+1,$fieldVariationData, $viewMode);
            $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode, [
                "fieldVariationData" => $fieldVariationData,
                "rendered"=> $renderedOutput[0],
            ])
                ->columnStart(1);
            $index+=$renderedOutput[1];
        }

        return [$customFormSchema,$index-$indexOffset];
    }

    //Todo Make InfoList Render

    //Todo Make save and load help for answars

}
