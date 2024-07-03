<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;

class EditCustomFormLoadHelper
{

    public static function load(CustomForm $form):array {
        $fields = $form->getOwnedFields();

        return [
            'custom_fields' => static::loadFields($fields),
            "custom_form_identifier" => $form->custom_form_identifier,
            'is_template' => $form->is_template,
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




}
