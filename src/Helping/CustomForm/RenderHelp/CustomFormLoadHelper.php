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
        foreach($answerer->cachedAnswers() as $fieldAnswer){
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

    public static function loadSplit(CustomFormAnswer $answerer, int $begin, int $end):array {
        $data = [];
        //$form = CustomForm::cached($answerer->custom_form_id);
        //$customFields = $form->cachedFields();

        $customFields = $answerer->customForm->customFieldsWithTemplateFields;
        $formRules  = $answerer->customForm->rules;

        foreach($answerer->cachedAnswers() as $fieldAnswer){
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
    }

    public static function runRulesForFieldData(CustomFormAnswer $answerer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) { //ToDo repair
            /**@var Rule $rule */
            $fieldData = $rule->handle(["action" => "load_answerer", "custom_field_answerer" => $answerer], $fieldData);
        }
        return $fieldData;
    }
}
