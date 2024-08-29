<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;

class EditCustomFormSaveHelper
{
    public static function save(array $rawState, CustomForm $form): void {
        $rawFields = $rawState["custom_fields"];
        $oldFields = $form->getOwnedFields();
        $fieldData = collect($rawFields);

        $fieldsToSaveData = [];
        $fieldsToCreate = [];
        $fieldsNotDirty = [];

        $now = now();

        //Prepare Save and Create Data
        foreach ($rawFields as $field) {

            if(!empty($field["id"]))
                $customField = $oldFields->where("id", $field["id"] )->first();
            else
                $customField = (new CustomField())->fill($field);

            $field['custom_form_id'] = $form->id;


            $field = CustomFieldUtils::getFieldTypeFromRawDate($field)
                ->getMutateCustomFieldDataOnSave($customField, $field);

            $customField->fill($field);

            $customField->getType()->doBeforeSaveField($customField, $field);


            if(!$customField->exists ){
                $rawField = $customField->getAttributes();
                $rawField['created_at'] = $now;
                $rawField['updated_at'] = $now;
                $fieldsToCreate[] = $rawField;
            }
            else if($customField->isDirty()) {
                $rawField = $customField->getAttributes();
                $rawField['updated_at'] = $now;
                $fieldsToSaveData[] = $rawField;
            }
            else  $fieldsNotDirty[] = $customField->id;
        }

        //Deleting
        $fieldsToDelete =  $oldFields
            ->whereNotIn('id', collect($fieldsToSaveData)->pluck("id"))
            ->whereNotIn("id", $fieldsNotDirty);

        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doBeforeDeleteField($field));
        CustomField::destroy($fieldsToDelete->pluck("id"));
        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doAfterDeleteField($field));


        //cleanUp
        $fieldsToCreate = self::cleanUpCustomFieldData($fieldsToCreate);

        $fieldsToSaveData = self::cleanUpCustomFieldData($fieldsToSaveData);

        //Create and Updating
        CustomField::insert($fieldsToCreate);
        CustomField::upsert($fieldsToSaveData, ['id']);

        //Run after Save
        $savedFields = $form->customFields()->where("custom_form_id", $form->id)->get();
        $savedFields
            ->whereIn('id', $fieldData->pluck("id"))
            ->each(fn(CustomField $field) =>
                $field->getType()->doAfterSaveField($field,
                    $fieldData->where("id", $field->id)->first()
                )
            );


        //Run after Create
        $savedFields = $form->customFields()->get();
        $savedFields
            ->whereNotIn('id', $fieldData->pluck("id"))
            ->each(function(CustomField $field) use ($fieldData) {

                if($field->isGeneralField()){
                    $savedFieldData = $fieldData->where("general_field_id", $field->general_field_id)->first();
                }
                else if($field->isTemplate()){
                    $savedFieldData = $fieldData->where("template_id", $field->template_id)->first();
                }
                else{
                    $savedFieldData = $fieldData->where("identifier", $field->identifier)->first();
                }

                return $field->getType()->doAfterCreateField($field, $savedFieldData??[]);
            }
        );


        //Rules
        $rawRules = $rawState["rules"];
        //Delete rules where doesnt exist
        $form->ownedRules->whereNotIn("id", collect($rawRules)->pluck("id"))->each(fn(Rule $rule)=> $rule->delete());

        $rules = collect();

        foreach ($rawRules as $rawRule) {
            if(!key_exists("id",$rawRule)) $rule = new Rule();
            else $rule = $form->ownedRules->where("id", $rawRule["id"])->first();

            $rawTriggers = $rawRule["triggers"]?? [];
            $rawEvents = $rawRule["events"] ?? [];


            $rule->ruleTriggers()->whereNotIn("id",collect($rawTriggers)->pluck("id"))->delete();
            $rule->ruleEvents()->whereNotIn("id", collect($rawEvents)->pluck("id"))->delete();


            if(key_exists("is_or_mode",$rawRule)) $rule->is_or_mode = $rawRule["is_or_mode"];
            else $rule->is_or_mode = false;

            $rule->save();
            $triggers = $rule->ruleTriggers;

            foreach ($rawTriggers as $rawTrigger) {
                if(!key_exists("id",$rawTrigger)) $trigger = new RuleTrigger();
                else $trigger = $triggers->where("id", $rawTrigger["id"])->first();

                $trigger->fill($rawTrigger);

                $trigger->rule_id =  $rule->id;
                $trigger->save();
            }

            foreach ($rawEvents as $rawEvent) {
                if(!key_exists("id",$rawEvent)) $event = new RuleEvent();
                else $event = $rule->ruleEvents()->where("id", $rawEvent["id"])->first();

                $event->fill($rawEvent);

                $event->rule_id =  $rule->id;
                $event->save();
            }


            $rules->add($rule);
        }


        $form->ownedRules()->sync($rules->pluck("id"));

        $form->cachedClear("customFields");
        CustomField::clearModelCache();

        
        $form->cachedClear("rules");
        $form->cachedClear("ownedRules");
        $form->cachedClear("formRules");
        Rule::clearModelCache();
    }

    private static function cleanUpCustomFieldData($fields): array
    {
        $columns = ['created_at', 'updated_at', 'id'];
        foreach ($fields as $rawField)  foreach ($rawField as $name => $value)  $columns[$name] = $name;
        foreach ($fields as $key => $rawField)
            foreach ($columns as $name )  {
                if(array_key_exists($name, $rawField)) continue;
                $fields[$key][$name] = null;
            }

        return $fields;
    }


}
