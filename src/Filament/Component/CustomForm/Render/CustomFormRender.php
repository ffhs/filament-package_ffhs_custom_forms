<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\FormRuleTriggerType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Filament\Forms\Components\Group;
use Illuminate\Support\Collection;

class CustomFormRender
{

    public static function generateFormSchema(CustomForm $form, string $viewMode):array{
        $customFields = $form->getOwnedFields();

        $render= self::getFormRender($viewMode,$form);
        $renderOutput = self::render(0,$customFields,$render,$viewMode, $form);

        return  [
            Group::make($renderOutput[0] ?? [])->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function getFormRender(string $viewMode,CustomForm $form): Closure {
        return function(CustomField $customField, array $parameter) use ($viewMode, $form) {
            //Render
            return $customField->getType()->getFormComponent($customField, $form, $viewMode, $parameter);
        };
    }

    public static function render(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode, CustomForm $customForm): array {
        if($customFields->isEmpty()) return [];


        $rules = $customForm->rules;

        //Rule before render
        $rules->each(function(Rule $rule) use (&$customFields) {
            $customFields = $rule->handle(["action" => "before_render"], $customFields);
        });

        //Render
        $renderOutput = self::renderRaw($indexOffset, $customFields, $render,$viewMode, $customForm);
        /**@var Collection $renderedComponent*/
        $renderedComponents = $renderOutput[1];


        //TriggerPreparation (maintain for live attribute) ca 30ms
        $rules
            ->map(fn(Rule $rule) => $rule->ruleTriggers)
            ->flatten(1)
            ->filter(fn(RuleTrigger $ruleTrigger) => !is_null($ruleTrigger))
            ->filter(fn(RuleTrigger $ruleTrigger) => $ruleTrigger->getType() instanceof FormRuleTriggerType)
            ->each(function (RuleTrigger $trigger) use (&$renderedComponents) {
                $renderedComponents = $trigger->getType()->prepareComponents($renderedComponents, $trigger);
            });


        $customFields = $customForm->customFields->mapWithKeys(fn(CustomField $field) => [$field->identifier => $field]);

        //Rule after Render
        $rules->each(function(Rule $rule) use ($customForm, $customFields, &$renderedComponents) {
          $renderedComponents = $rule->handle(["action" => "after_render", "custom_fields" => $customFields], $renderedComponents);
        });


        return $renderOutput;
    }

    public static function renderRaw(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode, CustomForm $form): array {
        $customFormSchema = [];
        $preparedFields = $customFields->keyBy("form_position");
//        $customFields->each(function(CustomField $field) use (&$preparedFields){
//            $preparedFields[$field->form_position] = $field;
//        });

        $allComponents = [];

        for($formPosition = $indexOffset+1; $formPosition<= $customFields->count()+$indexOffset; $formPosition++){

            /** @var CustomField $customField*/
            $customField =  $preparedFields[$formPosition];
            $parameters = ["viewMode"=>$viewMode];

            if(($customField->getType() instanceof CustomLayoutType)){
                $endLocation = $customField->layout_end_position;

                //Setup Render Data
                $fieldRenderData = [];
                for ($formPositionSubForm = $customField->form_position + 1; $formPositionSubForm <= $endLocation; $formPositionSubForm++) {
                    $fieldRenderData[] = $preparedFields[$formPositionSubForm];
                }
                $fieldRenderData = collect($fieldRenderData);


                //This Function allows to register the rendered components to $allComponents for the rules
                $registerRenderedComponents = function (array $components) use (&$allComponents){
                    $allComponents = array_merge($allComponents, $components);
                };

                //Render Schema Input
                $rendererFunction = function () use ($form, $viewMode, $render, $fieldRenderData, $formPosition, $registerRenderedComponents, &$allComponents){
                    $renderOutput = self::renderRaw($formPosition, $fieldRenderData, $render, $viewMode, $form);
                    $registerRenderedComponents($renderOutput[1]);
                    return $renderOutput[0];
                };

                //renderer should not be use in any schema closure function
                $parameters = array_merge([
                    "customFieldData" => $fieldRenderData,
                    "renderer" => $rendererFunction,
                    "registerComponents" => $registerRenderedComponents,
                ],$parameters);

                //Set Index
                $formPosition += $fieldRenderData->count(); //$renderedOutput[1] - 1; ToDo check if it works
            }

            if(($customField->getType() instanceof TemplateFieldType)){
                //Setup Render Data
                $fields = $customField->template->customFields;

                //Render Schema Input
                $renderedOutput = self::renderRaw(0, $fields, $render, $viewMode, $form);
                //Get Layout Schema
                $parameters["rendered"] = $renderedOutput[0];

                $allComponents = array_merge($allComponents, $renderedOutput[1]);
            }

            if(!$customField->is_active) continue;

            //Render
            $renderedComponent = $render($customField, $parameters);
            $allComponents[$customField->identifier] = $renderedComponent;

            $customFormSchema[] = $renderedComponent;
        }


        return [$customFormSchema, $allComponents];
    }

    public static function generateInfoListSchema(CustomFormAnswer $formAnswer, string $viewMode):array {
        $form = CustomForm::cached($formAnswer->custom_form_id);
        $customFields = $form->getOwnedFields();
        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= self::getInfolistRender($viewMode,$form,$formAnswer, $fieldAnswers);
        $customViewSchema = self::render(0,$customFields,$render, $viewMode, $formAnswer->customForm)[0];

        //ToDo Manage Components

        return  [
            \Filament\Infolists\Components\Group::make($customViewSchema)->columns(config("ffhs_custom_forms.default_column_count")),
        ];
    }

    public static function getInfolistRender(string $viewMode, CustomForm $form, CustomFormAnswer $formAnswer, Collection $fieldAnswers): Closure {
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

}

