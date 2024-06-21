<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

class FormBuilderUtil
{
    public static function build(?CustomForm $customForm, array $layout):CustomForm {
        if(is_null($customForm)) $customForm = CustomForm::create();
        $customField = [];

        static::generateFields($customForm, $layout, $customField);

        #$fixedFields = self::fixingRawFields($customField);

        foreach ($customField as $data){ //ToDo Optimize
            $rule = $data['rules'] ?? [];
            $customOptions = $data['customOptions'] ?? [];

            $names = $data['name'] ?? [];
            #$toolTips = $data['tool_tip'] ?? [];


            unset($data['name']);
            #unset($data['tool_tip']);
            unset($data['rules']);
            unset($data['customOptions']);

            $field = new CustomField();
            $field->fill($data);

            foreach ($names as $local => $name) $field->setTranslation("name", $local, $name);
            #foreach ($toolTips as $local => $toolTip) $field->setTranslation("tool_tip", $local, $toolTip);

            $field->save();

            $field->customOptions()->createMany($customOptions);
            $field->fieldRules()->createMany($rule);
        }

        return $customForm;
    }



   /* public static function fixingRawFields(array $fields):array {
        $filable = (new CustomField())->getFillable();
        $data = [];
        foreach ($fields as $field){
            $fieldData = [];
            foreach ($filable as $key ){
                if(array_key_exists($key, $field))  $fieldData[$key] = $field[$key];
                else $fieldData[$key] = null;
            }
            $data[] = $fieldData;
        }
        return $data;
    }*/

    public static function generateFields(CustomForm $form, array $fields, array &$toCreateFields): void {
        foreach ($fields as $field){
            $formPosition = sizeof($toCreateFields) + 1;

            $fieldData = [
                'form_position' => $formPosition,
                'custom_form_id' => $form->id,
                'is_active' => $field['is_active'] ?? true,
                #'required' => $field['required'] ?? false,
            ];

            $defaultOptions = [];
            if(array_key_exists('general_field', $field)){
                $genField = GeneralField::cached($field['general_field'],'identify_key');
                $fieldData['general_field_id'] = $genField->id;
                $defaultOptions = $genField->getType()->getDefaultTypeOptionValues();
            }
            else if(array_key_exists('general_field_id', $field)){
                $fieldData['general_field_id'] = $field['general_field_id'];
                $genField = GeneralField::cached($field['general_field_id']);
                $defaultOptions = $genField->getType()->getDefaultTypeOptionValues();
            }
            else if(array_key_exists('template', $field)){
                $template = CustomForm::cached($field['template'],'short_title');
                $fieldData['template_id'] = $template->id;
            }
            else if(array_key_exists('template_id', $field)){
                $fieldData['template_id'] = $field['template_id'];
            }
            else {

                //Names and ToolTips
                $fieldData['name'] = [];
                # $fieldData['tool_tip'] = [];
                foreach ($field as $key => $value){
                    if(str_contains($key, "name_")){
                        $fieldData['name'][str_replace("name_", "", $key)] = $value ;
                    }

                  /*
                   *   if(str_contains($key, "tool_tip_"))
                        $fieldData['tool_tip'][str_replace("tool_tip_", "", $key)] = $value ;
                   */
                }


                $fieldData['type'] = $field['type'];
                $fieldData['identifier']= $field['identifier'] ?? uniqid();
                $defaultOptions = CustomFieldType::getTypeFromIdentifier($field['type'])->getDefaultTypeOptionValues();
            }


            //Field Type Options
            self::prepareOptionData($field, $fieldData, $defaultOptions);

            //CustomOptions
            self::prepareCustomOptions($field, $fieldData);


            //Rules
            self::prepareRule($field, $fieldData);

            //Layout Fields
            self::setupLayoutFields($form, $field,$fieldData, $toCreateFields);

            $toCreateFields[] = $fieldData;

        }
    }



    private static function prepareOptionData(array $field, array &$fieldData, array $defaultOptions): void {
        $fieldData['options'] = array_merge($defaultOptions, $field['options'] ?? []);
    }


    private static function setupLayoutFields(CustomForm $form, array $field, array &$fieldData , array &$toCreateFields): void {
        if (!array_key_exists('fields', $field))  return;

        $placeHolderId = uniqid();

        $toCreateFields["placeHolder-".$placeHolderId] = $fieldData;
        static::generateFields($form, $field['fields'], $toCreateFields);
        $formEndPosition = sizeof($toCreateFields);

        unset($toCreateFields["placeHolder-".$placeHolderId]);
        $fieldData['layout_end_position'] = $formEndPosition;
    }


    //ToDo Name Converter
    private static function prepareCustomOptions(array $field, array &$fieldData): void {
        if (!array_key_exists("customOptions", $field)) return;
        $fieldData['customOptions'] = [];
        foreach ($field['customOptions'] as $customOption) {
            $fieldData['customOptions'][] = [
                'name_de' => $customOption["name_de"],
                'name_en' => $customOption["name_en"],
                'identifier' => $customOption["identifier"] ?? uniqid(),
            ];
        }
    }


    private static function prepareRule(array $field, array &$fieldData): void {
        if (!array_key_exists("rules", $field)) return;
        $counter = 0;
        $fieldData['rules'] = [];
        foreach ($field['rules'] as $rule) {
            $counter++;

            $anchorType = $rule["anchor_identifier"];
            $ruleType = $rule["rule_identifier"];

            $anchorData = $rule["anchor_data"] ?? [];
            $anchorData = array_merge(FieldRuleAnchorType::getTypeFromIdentifier($anchorType)->getCreateAnchorData(),$anchorData);

            $rule_data = $rule["rule_data"] ?? [];
            $rule_data = array_merge(FieldRuleType::getTypeFromIdentifier($ruleType)->getCreateRuleData(), $rule_data);

            $fieldData['rules'][] = [
                'anchor_identifier' => $anchorType,
                'rule_identifier' => $ruleType,
                'anchor_data' => $anchorData,
                'rule_data' => $rule_data,
                'execution_order' => $counter,
            ];
        }
    }


}
