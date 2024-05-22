<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
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
        $customFormSchema = self::render(0,$customFields,$render,$viewMode)[0];

        return  [
            Group::make($customFormSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }


    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->cachedFields();
        $fieldAnswers = $formAnswer->cachedAnswers();

        $render= self::getInfolistRender($viewMode,$form,$formAnswer, $fieldAnswers);
        $customViewSchema = self::render(0,$customFields,$render, $viewMode)[0];

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
            $component->live(true);

            return $component;
        };
    }

    public static function render(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode): array {
        $customFormSchema = [];

        $preparedFields = [];
        $customFields->each(function(CustomField $field) use (&$preparedFields){
            $preparedFields[$field->form_position] = $field;
        });


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
                $renderedOutput = self::render($index, $fieldRenderData, $render,$viewMode);
                //Get Layout Schema
                $parameters = array_merge([
                    "customFieldData" => $fieldRenderData,
                    "rendered"=> $renderedOutput[0],
                ]);

                //Set Index
                $index= $renderedOutput[1]-1;
            }

            if(!$customField->is_active) continue;

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
}
