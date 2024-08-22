<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

trait HasFormTargets
{

    public function getTargetsSelect(): Select{
        return Select::make('targets')
            ->multiple()
            ->label("Target")
            ->options($this->getTargetOptions())
            ->hidden(function ($set, $get){
                //Fields with an array doesn't generate properly
                if($get('targets') == null)
                $set("targets",[]);
            })
            ->live();
    }

    public function getTargetSelect(): Select{
        return Select::make('target')
            ->label("Target")
            ->options($this->getTargetOptions())
            ->live();
    }


    public function getTargetOptions(): \Closure
    {
        return function ($get) {
           return collect($this->getAllFieldsData($get))
                ->map(fn($fieldData) => (new CustomField())->fill($fieldData))
                ->pluck("name", "identifier");
        };
    }



    public function getTargetFieldData(Get $get):array|null {
        $identifier = $get("target");
        if(is_null($identifier)) return null;

        $fields = $this->getAllFieldsData($get);

        //Search the target field
        $finalField = null;

        foreach ($fields as $field){
            $customField = new CustomField();
            $customField->fill($field);
            if($customField->identifier != $identifier) continue;
            $finalField = $field;
            break;
        }
        return $finalField;
    }


    public function getAllFieldsData(Get $get): array
    {
        $fields = $get("../../../../../custom_fields")??[] ;

        //Get the templated FormComponents
        $fieldsFromTemplate = collect($fields)
            ->whereNotNull("template_id")
            ->map(fn($templateData) => CustomForm::cached($templateData["template_id"]))
            ->map(fn($template) => $template->customFields
                ->map(fn(CustomField $customField) => EditCustomFormLoadHelper::loadField($customField))
            )
            ->flatten(1)
            ->toArray();

        return array_merge($fieldsFromTemplate, $fields);
    }

}
