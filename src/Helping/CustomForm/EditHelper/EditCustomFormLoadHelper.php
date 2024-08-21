<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Support\Collection;

class EditCustomFormLoadHelper
{

    public static function load(CustomForm $form):array {
        $fields = $form->getOwnedFields();

        return [
            'custom_fields' => static::loadFields($fields),
            "custom_form_identifier" => $form->custom_form_identifier,
            'is_template' => $form->is_template,
            'id' => $form->id,
            'rules' => static::loadRules($form)
        ];
    }



    public static function loadFields(Collection $fields):array {
        $data = [];
        foreach ($fields as $field) {
            /**@var CustomField $field*/
            $fieldData = $field->attributesToArray();
            $fieldData['options'] = $field->options;

            unset($fieldData["updated_at"]);
            unset($fieldData["created_at"]);

            $fieldData = $field->getType()->getMutateCustomFieldDataOnLoad($field, $fieldData);

            $key = EditCustomFormHelper::getEditKey($field);
            $data[$key] = $fieldData;
        }
        return $data;
    }

    private static function loadRules(CustomForm $form)
    {
        $rules = [];
        foreach ($form->rules as $rule) {
            /**@var Rule $rule*/
            $rawRule = $rule->toArray();
            $rawRule['events'] = $rule->ruleEvents->toArray();
            $rawRule['triggers'] = $rule->ruleTriggers->toArray();
            $rules[] = $rawRule;
        }
        return $rules;
    }


}
