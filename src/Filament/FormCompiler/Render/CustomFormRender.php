<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Filament\Forms\Components\Group;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode):array{
        $customFields = $form->getOwnedFields();

        $render= self::getFormRender($viewMode,$form);
        $renderOutput = self::render(0,$customFields,$render,$viewMode);

        return  [
            Group::make($renderOutput[0])->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->getOwnedFields();
        $fieldAnswers = $formAnswer->cachedAnswers();

        $render= self::getInfolistRender($viewMode,$form,$formAnswer, $fieldAnswers);
        $customViewSchema = self::render(0,$customFields,$render, $viewMode)[0];

        //ToDo Manage Components

        return  [
            \Filament\Infolists\Components\Group::make($customViewSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function getInfolistRender(string $viewMode,CustomForm $form, CustomFormAnswer $formAnswer, Collection $fieldAnswers): Closure {
        return function (CustomField $customField,  array $parameter) use ($formAnswer, $form, $viewMode, $fieldAnswers) {

            /** @var CustomFormAnswer $answer*/
            $answer = $fieldAnswers->firstWhere("custom_field_id", $customField->id);
            if (is_null($answer)) {
                $answer = new CustomFieldAnswer();
                $answer->answer = null;
                $answer->custom_field_id = $customField->id;
                $answer->custom_form_answer_id = $formAnswer->id;
            }

            return $customField->getType()->getInfolistComponent($answer, $form, $viewMode, $parameter);
        };
    }

    public static function getFormRender(string $viewMode,CustomForm $form): Closure {
        return function(CustomField $customField, array $parameter) use ($viewMode, $form) {

            //Render
            $component = $customField->getType()->getFormComponent($customField, $form, $viewMode, $parameter);
         //   if($component instanceof Field) $component->required($customField->required); //ToDo
           // $component->live(true);

            return $component;
        };
    }


    public static function render(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode): array {
        if($customFields->isEmpty()) return [];

        $renderOutput = self::renderRaw($indexOffset, $customFields, $render,$viewMode);
        $components = $renderOutput[3];
        $customForm = $customFields->first()->custom_form;

        //Run Rules after rendered
        $renderOutput[2]->each(function(Rule $rule) use ($customForm, &$components) {
            $components = $rule->handle(["action" => "after_all_rendered", "customForm" => $customForm], $components);
        });
        return $renderOutput;
    }

    public static function renderRaw(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode): array {
        $customFormSchema = [];

        $preparedFields = [];
        $customFields->each(function(CustomField $field) use (&$preparedFields){
            $preparedFields[$field->form_position] = $field;
        });

        $allRules = collect();
        $allComponents = collect();

        for($index = $indexOffset+1; $index<= $customFields->count()+$indexOffset; $index++){

            /** @var CustomField $customField*/
            $customField =  $preparedFields[$index];
            $parameters = [
                "viewMode"=>$viewMode
            ];

            if(($customField->getType() instanceof CustomLayoutType)){

                $endLocation = $customField->layout_end_position;

                //Setup Render Data
                $fieldRenderData = [];
                for($formPositionSubForm = $customField->form_position+1; $formPositionSubForm <= $endLocation; $formPositionSubForm++){
                    $fieldRenderData[] =  $preparedFields[$formPositionSubForm];
                }
                $fieldRenderData = collect($fieldRenderData);

                //Render Schema Input
                $renderedOutput = self::renderRaw($index, $fieldRenderData, $render,$viewMode);
                //Get Layout Schema
                $parameters = array_merge([
                    "customFieldData" => $fieldRenderData,
                    "rendered"=> $renderedOutput[0],
                ]);

                //Set Index
                $index= $renderedOutput[1]-1;
                $allRules = $allRules->merge($renderedOutput[2]);
                $allComponents = $allComponents->merge($renderedOutput[3]);
            }

            if(!$customField->is_active) continue;

            $rules = $customField->customForm->rules;

            //Rule before render
            $rules->each(function(Rule $rule) use (&$customField) {
                $customField = $rule->handle(["action" => "before_render",  "customField" => $customField], $customField);
            });

            //Parameter mutation
            $rules->each(function(Rule $rule) use ($customField, &$parameters) {
                $parameters = $rule->handle(["action" => "mutate_parameters", "customField" => $customField], $parameters);
            });

            //Render
            $renderedComponent = $render($customField, $parameters);

            //Rule after Render
            $rules->each(function(Rule $rule) use ($customField, &$renderedComponent) {
                $renderedComponent = $rule->handle(["action" => "after_render", "customField" => $customField], $renderedComponent);
            });
            $customFormSchema[] = $renderedComponent;

            $allRules = $allRules->merge($rules);
            $allComponents->add($renderedComponent);
        }
        return [$customFormSchema,$index, $allRules, $allComponents];
    }
}

