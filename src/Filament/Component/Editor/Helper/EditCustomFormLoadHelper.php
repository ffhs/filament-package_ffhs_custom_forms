<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;

class EditCustomFormLoadHelper
{

    public static function load(CustomForm $form):array {
        $fieldData =  [];

        $fields = $form->getOwnedFields();

        $fieldData["data"] = static::loadFields($fields);
        $fieldData["structure"] = static::loadStructure($fields);

        return ['custom_fields' => $fieldData, "custom_form_identifier" => $form->custom_form_identifier];
    }



    public static function loadFields(Collection $fields):array {
        $data = [];
        foreach ($fields as $field) {
            /**@var CustomField $field*/
            $fieldData = $field->attributesToArray();

            unset($fieldData["updated_at"]);
            unset($fieldData["created_at"]);

            $fieldData = $field->getType()->getMutateCustomFieldDataOnLoad($field, $fieldData);

            $data[$field->identifier] = $fieldData;
        }
        return $data;
    }


    private static function loadStructure(Collection $fields): array {
        if($fields->count() === 0) return [];

        $fields = $fields->sortBy('form_position');
        $start = array_values($fields->toArray())[0]['form_position'];
        $end = array_values($fields->toArray())[$fields->count()-1]['form_position'];

        $structure = [];

        for ($i = $start; $i <= $end; $i++) {
            /**@var CustomField $field */
            $field = $fields->firstWhere('form_position', $i);

            if(empty($field->layout_end_position)) {
                $structure[$field->identifier] = [];
                continue;
            }

            $subFields = $field->customForm->getOwnedFields()
                ->where("form_position", ">", $field->form_position)
                ->where("form_position", "<=", $field->layout_end_position);


            $i = $field->layout_end_position;
            $structure[$field->identifier] = static::loadStructure($subFields);
        }


        return  $structure;

    }


}
