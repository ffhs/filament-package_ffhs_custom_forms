<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Group;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode):array{
        $customFields = $form->cachedFields();

        $render= self::getFormRender($viewMode,$form);
        $renderOutput = self::render(0,$customFields,$render,$viewMode);

        return  [
            Group::make($renderOutput[0])->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->cachedFields();
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
            if($component instanceof Field) $component->required($customField->required);
           // $component->live(true);

            return $component;
        };
    }


    public static function render(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode): array {
        $renderOutput = self::renderRaw($indexOffset, $customFields, $render,$viewMode);
        $components = $renderOutput[3];

        //Run Rules after rendered
        $renderOutput[2]->each(function(FieldRule $rule) use ($components) {
            $rule->getAnchorType()->afterAllFormComponentsRendered($rule,$components);
            $rule->getRuleType()->afterAllFormComponentsRendered($rule,$components);
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

            $rules = $customField->fieldRules;

            //Rule before render
            $rules->each(function(FieldRule $rule) {
                $rule->getRuleType()->beforeComponentRender($rule);
            });

            //Parameter anchor mutation
            $rules->each(function(FieldRule $rule) use (&$parameters) {
                $parameters =  $rule->getAnchorType()->mutateRenderParameter($parameters,$rule);
            });

            //Parameter rule mutation
            $rules->each(function(FieldRule $rule) use (&$parameters,) {
                $parameters =  $rule->getRuleType()->mutateRenderParameter($parameters,$rule);
            });

            //Render
            $renderedComponent = $render($customField, $parameters);

            //Rule after Render
            $rules->each(function(FieldRule $rule) use (&$renderedComponent) {
                $renderedComponent = $rule->getRuleType()->afterComponentRender($renderedComponent,$rule);
            });
            $customFormSchema[] = $renderedComponent;

            $allRules = $allRules->merge($rules);
            $allComponents->add($renderedComponent);
        }
        return [$customFormSchema,$index, $allRules, $allComponents];
    }
}
