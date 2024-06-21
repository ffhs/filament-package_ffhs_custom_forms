<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;

class EditCustomFormLoadHelper
{

    public static function load(CustomForm $form):array {
        $fieldData =  [];

        $fields = $form->getOwnedFields();

        $fieldData["custom_fields"] = static::loadFields($fields);

        return $fieldData;
    }


    public static function loadFields(Collection $fields):array {
        if($fields->count() === 0) return [];

        $fields = $fields->sortBy('form_position');
        $start = array_values($fields->toArray())[0]['form_position'];
        $end = array_values($fields->toArray())[$fields->count()-1]['form_position'];

        $fieldDatas = [];

        for ($i = $start; $i <= $end; $i) {
            /**@var CustomField $field */
            $field = $fields->firstWhere('form_position', $i);
            [$newPos, $data] = $field->loadEditData();

            $fieldDatas[$field->identifier] = $data;
            $i = $newPos;
        }

        return $fieldDatas;
    }


}
