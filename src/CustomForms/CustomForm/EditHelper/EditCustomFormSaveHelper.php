<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\EditHelper;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Support\Collection;

class EditCustomFormSaveHelper
{


    public static function savingRules($rules1, CustomForm $form): void
    {
        $rawRules = $rules1;
        //Delete rules where doesnt exist
        $form->ownedRules->whereNotIn("id", collect($rawRules)->pluck("id"))->each(fn(Rule $rule) => $rule->delete());

        $rules = collect();

        self::saveRuleComponents($rawRules, $form, $rules);


        $form->ownedRules()->sync($rules->pluck("id"));
    }

    public static function saveRuleComponents(mixed $rawRules, CustomForm $form, Collection $rules): void
    {
        foreach ($rawRules as $rawRule) {
            if (key_exists("id", $rawRule))
                $rule = $form->ownedRules->where("id", $rawRule["id"])->first();
            else $rule = new Rule();


            $rawTriggers = $rawRule["triggers"] ?? [];
            $rawEvents = $rawRule["events"] ?? [];


            $rule->ruleTriggers()->whereNotIn("id", collect($rawTriggers)->pluck("id"))->delete();
            $rule->ruleEvents()->whereNotIn("id", collect($rawEvents)->pluck("id"))->delete();


            if (key_exists("is_or_mode", $rawRule))
                $rule->is_or_mode = $rawRule["is_or_mode"];
            else $rule->is_or_mode = false;


            $rule->save();
            $triggers = $rule->ruleTriggers;
            $events = $rule->ruleEvents;

            self::updateRuleComponent($rawTriggers, $triggers, $rule, RuleTrigger::class);
            self::updateRuleComponent($rawEvents, $events, $rule, RuleEvent::class);


            $rule->cachedClear("ruleTriggers");
            $rule->cachedClear("ruleEvents");
            $rules->add($rule);
        }
    }

    public static function save(array $rawState, CustomForm $form): void {

        //Fields
        self::saveFields($rawState["custom_fields"], $form);

        //Rules
        self::savingRules($rawState["rules"], $form);


        //Clear Cache
        $form->cachedClear("customFields");
        $form->cachedClear("rules");
        $form->cachedClear("ownedRules");
        $form->cachedClear("formRules");

//        RuleEvent::clearModelCache();
//        RuleTrigger::clearModelCache();
//        CustomField::clearModelCache();
//        Rule::clearModelCache();
    }

    public static function saveFields($custom_fields, CustomForm $form): void
    {
        $rawFields = $custom_fields;
        $oldFields = $form->getOwnedFields();
        $fieldData = collect($rawFields);

        list($fieldsToSaveData, $fieldsToCreate, $fieldsNotDirty) = self::prepareFields($rawFields, $oldFields, $form);

        //Deleting
        self::deletingFields($oldFields, $fieldsToSaveData, $fieldsNotDirty);

        //cleanUp
        $fieldsToCreate = self::cleanUpCustomFieldData($fieldsToCreate);
        $fieldsToSaveData = self::cleanUpCustomFieldData($fieldsToSaveData);

        //Create and Updating
        self::createAndUpdatingFields($fieldsToCreate, $fieldsToSaveData, $form, $fieldData);
    }

    public static function prepareFields(mixed $rawFields, Collection $oldFields, CustomForm $form): array
    {
        $fieldsToSaveData = [];
        $fieldsToCreate = [];
        $fieldsNotDirty = [];

        $now = now();

        //Prepare Save and Create Data
        foreach ($rawFields as $field) {
            if (!empty($field["id"])) {
                $customField = $oldFields->where("id", $field["id"])->first();
            } else {
                $customField = (new CustomField())->fill($field);
            }

            $field['custom_form_id'] = $form->id;


            $field = CustomFieldUtils::getFieldTypeFromRawDate($field)
                ->getMutateCustomFieldDataOnSave($customField, $field);

            $customField->fill($field);

            $customField->getType()
                ->doBeforeSaveField($customField, $field);


            if (!$customField->exists) {
                $rawField = $customField->getAttributes();
                $rawField['created_at'] = $now;
                $rawField['updated_at'] = $now;
                $fieldsToCreate[] = $rawField;
            } else {
                if ($customField->isDirty()) {
                    $rawField = $customField->getAttributes();
                    $rawField['updated_at'] = $now;
                    $fieldsToSaveData[] = $rawField;
                } else {
                    $fieldsNotDirty[] = $customField->id;
                }
            }
        }
        return array($fieldsToSaveData, $fieldsToCreate, $fieldsNotDirty);
    }

    public static function deletingFields(Collection $oldFields, array $fieldsToSaveData, array $fieldsNotDirty): void {
        $fieldsToDelete = $oldFields
            ->whereNotIn('id', collect($fieldsToSaveData)->pluck("id"))
            ->whereNotIn("id", $fieldsNotDirty);

        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doBeforeDeleteField($field));
        CustomField::destroy($fieldsToDelete->pluck("id"));
        $fieldsToDelete->each(fn(CustomField $field) => $field->getType()->doAfterDeleteField($field));
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

    public static function createAndUpdatingFields(
        array $fieldsToCreate,
        array $fieldsToSaveData,
        CustomForm $form,
        Collection $fieldData
    ): void {


        //Create and Updating
        CustomField::insert($fieldsToCreate);
        CustomField::upsert($fieldsToSaveData, ['id']);

        //Run after Save
        $savedFields = $form->ownedFields()->get();

        $savedFields
            ->whereIn('id', $fieldData->pluck("id"))
            ->each(function(CustomField $field) use ($fieldData): void {
                $data = $fieldData->where("id", $field->id)->first();
                $field
                    ->getType()
                    ->doAfterSaveField($field, $data);
            });


        //Run after Create
        $savedFields
            ->whereNotIn('id', $fieldData->pluck("id"))
            ->each(function (CustomField $field) use ($fieldData): void {
//                if ($field->isGeneralField()) { ToDo Remove not used code
//                    $savedFieldData = $fieldData->where("general_field_id", $field->general_field_id)->first();
//                } else {
//                    if ($field->isTemplate()) {
//                        $savedFieldData = $fieldData->where("template_id", $field->template_id)->first();
//                    } else {
//                        $savedFieldData = $fieldData->where("identifier", $field->identifier)->first();
//                    }
//                }
                $data = $fieldData->where("identifier", $field->identifier)->first()?? [];
                $field->getType()->doAfterCreateField($field, $data);
            }
            );
    }

    public static function updateRuleComponent(mixed $rawComponents, Collection $components, Rule $rule, string $type): void
    {
        foreach ($rawComponents as $rawComponent) {
            if (!key_exists("id", $rawComponent))
                $ruleComponent = new $type();
            else
                $ruleComponent = $components->where("id", $rawComponent["id"])->first();

            $ruleComponent->fill($rawComponent);

            $ruleComponent->rule_id = $rule->id;
            $ruleComponent->save();
        }
    }

}
