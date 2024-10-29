<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;

class CustomFormLoadHelper {

    public static function load(CustomFormAnswer $answerer):array {
        $data = [];
       //ToDo check to Cache stuff for performance $customFields = $answerer->customForm->customFields;

        $formRules = $answerer->customForm->rules;
        foreach($answerer->customFieldAnswers as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            /**@var CustomField $customField*/
            $customField = $fieldAnswer->customField;
            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);

            $fieldData = self::runRulesForFieldData($answerer, $fieldData,$formRules);

            $data[$customField->identifier] = $fieldData;
        }
        return $data;
    }

    public static function runRulesForFieldData(CustomFormAnswer $answerer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) { //ToDo repair
            /**@var Rule $rule */
            $fieldData = $rule->handle(["action" => "load_answerer", "custom_field_answerer" => $answerer], $fieldData);
        }
        return $fieldData;
    }

    public static function loadSplit(CustomFormAnswer $answerer, int $begin, int $end):array {
        $data = [];

        $customFields = $answerer->customForm->customFields;
        $formRules  = $answerer->customForm->rules;

        foreach($answerer->customFieldAnswers as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            /**@var CustomField $customField*/
            $customField = $fieldAnswer->customField;

            if($begin > $customField->form_position || $customField->form_position > $end){

                if($answerer->custom_form_id == $customField->custom_form_id) continue;

                /**@var CustomField|null $templateField*/
                $templateField = $customFields->where("template_id",$customField->custom_form_id)->first();

                if(!$templateField) continue;

                if($begin > $templateField->form_position || $templateField->form_position > $end) continue;

            }

            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);


            $fieldData = self::runRulesForFieldData($answerer, $fieldData, $formRules);
            $data[$customField->identifier] = $fieldData;
        }
        return $data;

//        $customFields = $answerer->customForm->customFields;
//
//        $fields = $customFields->where("form_position", ">=", $begin)->where("form_position", "<=", $end);
//        $fields->merge($customFields->whereIn("custom_form_id", $fields->whereNotNull('template_id')->pluck("id")));
//
//        $identifiers = $fields->map(fn (CustomField $field) => $field->identifier)->toArray();
//
//        $allAnswerers =  $answerer->cachedLoadedAnswares();
//
//        return  array_filter($allAnswerers, function($key) use ($identifiers) {
//            return in_array($key, $identifiers);
//        }, ARRAY_FILTER_USE_KEY);

    }
}
