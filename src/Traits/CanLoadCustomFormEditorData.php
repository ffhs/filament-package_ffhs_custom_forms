<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\EditHelper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Support\Collection;

trait CanLoadCustomFormEditorData
{
    public function loadCustomFormEditorData(CustomForm $customForm): array
    {
        $fields = $customForm->getOwnedFields();

        return [
            'custom_fields' => $this->loadEditorFields($fields),
            'custom_form_identifier' => $customForm->custom_form_identifier,
            'is_template' => $customForm->is_template,
            'id' => $customForm->id,
            'rules' => $this->loadEditorRules($customForm)
        ];
    }

    public function loadEditorField(CustomField $field): array
    {
        $fieldData = $field->attributesToArray();
        $fieldData['options'] = $field->options;

        unset($fieldData["updated_at"], $fieldData["created_at"]);

        $type = $field->getType();
        $fieldData = $type->getMutateCustomFieldDataOnLoad($field, $fieldData);
        $fieldData['cachedFieldType'] = $type::class;

        return $fieldData;
    }

    protected function loadEditorFields(Collection $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $key = $this->getFieldEditorKey($field);
            $data[$key] = $this->loadEditorField($field);
        }
        return $data;
    }

    protected function getFieldEditorKey(array|CustomField $toAdd)
    {
        if ($toAdd instanceof CustomField) {
            return empty($field->identifier) ? uniqid() : $field->identifier;
        }

        if (array_key_exists("identifier", $toAdd) && !empty($toAdd["identifier"])) {
            return $toAdd["identifier"];
        }

        return uniqid();
    }

    protected function loadEditorRules(CustomForm $form): array
    {
        $rules = [];
        foreach ($form->ownedRules as $rule) {
            /**@var Rule $rule */
            $rawRule = $rule->toArray();
            $rawRule['events'] = $rule->ruleEvents->toArray();
            $rawRule['triggers'] = $rule->ruleTriggers->toArray();
            $rules[] = $rawRule;
        }
        return $rules;
    }
}
