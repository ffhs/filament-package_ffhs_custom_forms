<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CustomFormLoadHelper {

    public static function load(CustomFormAnswer $answerer, int|null $begin = null, int|null $end  = null):array {
        //ToDo check to Cache stuff for performance $customFields = $answerer->customForm->customFields;
        $loadedData = [];

        $formRules  = $answerer->customForm->rules;

        foreach($answerer->customFieldAnswers as $fieldAnswer){ 
            /**@var CustomFieldAnswer $fieldAnswer*/
            /**@var CustomField $customField*/
            /**@var CustomFieldType $type*/
            $customField = $fieldAnswer->customField;

            $beginCondition = is_null($begin) || $begin < $customField->form_position;
            $endCondition = is_null($end) || $customField->form_position < $end;

            if(!($beginCondition && $endCondition)) continue;

            $fieldData = $customField
                ->getType()
                ->prepareLoadFieldData($fieldAnswer->answer);

            $fieldData = static::runRulesForFieldData($answerer, $fieldData, $formRules); //10ms

            $dataIdentifier = $customField->identifier;
            $dataIdentifier .= empty($fieldAnswer->path)? '' :  "." . $fieldAnswer->path;

            $loadedData[$dataIdentifier] = $fieldData;
        }

        $fields = $answerer->customForm
            ->customFields
            ->sortBy("form_position")
            ->keyBy(fn (CustomField $item) => $item->identifier);

        foreach ($loadedData as $key => $data){
            if(!str_contains($key, ".")) continue;
            $keyPath = explode(".", $key);
            $identifier = $keyPath[0];

            $path = implode('.', array_slice($keyPath, 1));
            $pathResolved = static::findPath($fields, $fields->get($identifier), $path) ."." . $identifier;

            Arr::set($loadedData, $pathResolved,$data);
            unset($loadedData[$key]);
        }

        return $loadedData;
    }


    public static function runRulesForFieldData(CustomFormAnswer $answerer, mixed $fieldData, $formRules): mixed
    {
        foreach ($formRules as $rule) {
            /**@var Rule $rule */
            $fieldData = $rule->handle(["action" => "load_answerer", "custom_field_answerer" => $answerer], $fieldData);
        }
        return $fieldData;
    }

    protected static function findPath(Collection $fields, CustomField $childField, string $path): string
    {
        /**@var CustomField $field*/
        $nearestParent = null;

        foreach ($fields as $field) {
            if($childField->custom_form_id != $field->custom_form_id) continue;
            if($field->form_position >= $childField->form_position) break;
            if($field->layout_end_position < $childField->form_position) continue;
            if(!$field->getType()->hasSplitFields()) continue;
            $nearestParent = $field;
        }


        $pathFragments = explode(".", $path);
        $path = end($pathFragments);
        $lastPath = sizeof($pathFragments) >1?  implode(" ", array_slice($pathFragments, 0, -1)) : null;

        if(is_null($nearestParent)) return "";
        else if(!is_null($lastPath)){
            $resultPathParent = static::findPath($fields, $nearestParent, $lastPath);
            return $nearestParent->identifier . ".". $path . "." . $resultPathParent;
        }

        return $nearestParent->identifier . ".". $path;
    }




}
