<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render;

use Closure;
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
        if($customFields->isEmpty()) return [[], collect()];
        $rules = $customForm->rules;

        //Rule before render ca 70ms
        $rules->each(function(Rule $rule) use (&$customFields) {
            $customFields = $rule->handle(["action" => "before_render"], $customFields);
        });

        //Render ca 72ms
        $renderOutput = self::renderRaw($indexOffset, $customFields, $render,$viewMode, $customForm);
        /**@var Collection $renderedComponent*/
        $renderedComponents = $renderOutput[1];




        //TriggerPreparation (maintain for live attribute)  ca 10ms
        $rules
            ->map(fn(Rule $rule) => $rule->ruleTriggers)
            ->flatten(1)
            ->filter(fn(RuleTrigger $ruleTrigger) => !is_null($ruleTrigger))
            ->filter(fn(RuleTrigger $ruleTrigger) => $ruleTrigger->getType() instanceof FormRuleTriggerType)
            ->each(function (RuleTrigger $trigger) use (&$renderedComponents) {
                $renderedComponents = $trigger->getType()->prepareComponents($renderedComponents, $trigger);
            });


        $customFields = $customForm->customFields->mapWithKeys(fn(CustomField $field) => [$field->identifier => $field]);

        //Rule after Render ca 2ms - 10ms
        $rules->each(function(Rule $rule) use ($customForm, $customFields, &$renderedComponents) {
          $renderedComponents = $rule->handle(["action" => "after_render", "custom_fields" => $customFields], $renderedComponents);
        });


        return $renderOutput;
    }

    public static function renderRaw(int $indexOffset, Collection $customFields, Closure &$render, string $viewMode, CustomForm $form): array {
        $customFormSchema = [];
        $preparedFields = $customFields->keyBy("form_position");
        $allComponents = [];
        //This Function allows to register the rendered components to $allComponents for the rules
        $registerRenderedComponents = function (array $components) use (&$allComponents){
            $allComponents = array_merge($allComponents, $components);
        };

        //Define Parameters
        $baseParameters = [
            "viewMode" => $viewMode,
            "registerComponents" => $registerRenderedComponents,
            "render" => $render,
        ];


        for($formPosition = $indexOffset+1; $formPosition<= $customFields->count()+$indexOffset; $formPosition++){

            if(empty($preparedFields[$formPosition])) continue;

            /** @var CustomField $customField*/
            $customField =  $preparedFields[$formPosition];

            //When field isn't Active skip it
            if(!$customField->is_active) continue;

            $parameters = $baseParameters;

            //if field is an layout field, add Render CComponents
            if(!is_null( $customField->layout_end_position)){
//
                $fieldRenderData = [];
                for ($formPositionSubForm = $customField->form_position + 1; $formPositionSubForm <= $customField->layout_end_position; $formPositionSubForm++) {
                    $fieldRenderData[] = $preparedFields[$formPositionSubForm];
                }
                $fieldRenderData = collect($fieldRenderData);
                $parameters["customFieldData"] = $fieldRenderData;


                //Render Schema Input
                $parameters["renderer"] = function () use ($fieldRenderData, &$parameters, $form, $viewMode, $render, $formPosition, $registerRenderedComponents, &$allComponents){
                    $renderOutput = self::renderRaw($formPosition, $fieldRenderData, $render, $viewMode, $form);
                    $registerRenderedComponents($renderOutput[1]);
                    return $renderOutput[0];
                };

                //Skip fields where in the sub renderer Index
                $formPosition += $fieldRenderData->count();
            }


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

    public static function getInfolistRender(string $viewMode, CustomForm $form, CustomFormAnswer $formAnswer, Collection $fieldAnswers, ?string $path = null): Closure {

        $fieldAnswers = $fieldAnswers->filter(function ($item) use ($path) {
            if(is_null($item['path'] ) || is_null($path)) return $path == $item['path'];
            return str_contains($item['path'], $path);
        });
        $fieldAnswers = $fieldAnswers->keyBy("custom_field_id");


        return function (CustomField $customField,  array $parameter) use ($path, $formAnswer, $form, $viewMode, $fieldAnswers) {

            //150us
            /** @var CustomFormAnswer $answer*/
            $answer = $fieldAnswers->get($customField->id);
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

