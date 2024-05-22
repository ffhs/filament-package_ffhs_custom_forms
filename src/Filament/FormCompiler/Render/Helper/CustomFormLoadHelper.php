<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;

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
            foreach ($fieldRules as $rule){
                /**@var FieldRule $rule */
                $fieldData = $rule->getRuleType()->mutateLoadAnswerData($fieldData,$rule, $fieldAnswer);
            }

            $data[$customField->getInheritState()["identify_key"]] = $fieldData;
        }
        return $data;
    }
}
