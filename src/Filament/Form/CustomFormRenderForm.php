<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;

class CustomFormRenderForm
{


    public static function generateFormSchema(CustomForm $form, string $viewMode, null|int|Model $variation = null):array{
        $customFormSchema = [];

        if(is_null($variation))
            $fieldVariations = $form->customFields->map(fn(CustomField $field) => $field->templateVariation());
        else
            $fieldVariations = $form->customFields->map(fn(CustomField $field) => $field->getVariation($variation));


        foreach($fieldVariations as $fieldVariation){
            /** @var CustomFieldVariation $fieldVariation*/

            if(!$fieldVariation->is_active) continue;

            /** @var CustomField $customField*/
            $customField = $form->customFields->firstWhere("id", $fieldVariation->custom_field_id);

            $customFormSchema[] = $customField->getType()->getFormComponent($fieldVariation,$viewMode);
        }


        return  [Repeater::make("form")->schema($customFormSchema)]; //ToDo change Stuff
    }

    //Todo Make InfoList Render

    //Todo Make save and load help for answars

}
