<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;

class CustomFormLoadHelper {

    public static function load(CustomFormAnswer $answerer):array {
        $data = [];
        //$form = CustomForm::cached($answerer->custom_form_id);
        //$customFields = $form->cachedFields();

        foreach($answerer->cachedAnswers() as $fieldAnswer){
            /**@var CustomFieldAnswer $fieldAnswer*/
            /**@var CustomField $customField*/
            $customField = $fieldAnswer->customField;
            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);

            $fieldRules  = $customField->fieldRules;
            foreach ($fieldRules as $rule){ //ToDo repair
                /**@var Rule $rule */
                $fieldData = $rule->getRuleType()->mutateLoadAnswerData($fieldData,$rule, $fieldAnswer);
            }

            $data[$customField->identifier] = $fieldData;
        }
        return $data;
    }

    public static function loadSplit(CustomFormAnswer $answerer, int $begin, int $end):array {
        $data = [];
        //$form = CustomForm::cached($answerer->custom_form_id);
        //$customFields = $form->cachedFields();

        $customFields = $answerer->customForm->customFieldsWithTemplateFields;

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

            $fieldRules  = $customField->fieldRules;
            foreach ($fieldRules as $rule){
                /**@var FieldRule $rule */
                $fieldData = $rule->getRuleType()->mutateLoadAnswerData($fieldData,$rule, $fieldAnswer);
            }

            $data[$customField->identifier] = $fieldData;
        }
        return $data;
    }
}
