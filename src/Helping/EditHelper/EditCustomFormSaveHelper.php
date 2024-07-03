<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class EditCustomFormSaveHelper
{
    public static function save(array $rawState, CustomForm $form): void {
        $oldFields = $form->customFields;
        $fieldData = collect($rawState);

        $fieldsToSaveData = [];
        $fieldsToCreate = [];
        $fieldsNotDirty = [];

        $now = now();

        //Prepare Save and Create Data
        foreach ($rawState as $field) {

            if(!empty($field["id"]))
                $customField = $oldFields->where("id", $field["id"] )->first();
            else
                $customField = new CustomField();

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
        $savedFields = $form->customFields()->get();
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

    }

    private static function getPositionOfField(string $targetKey, array $structure, int &$index = 0): ?array {
        foreach ($structure as $key => $fields) {
            $index+= 1;
            if($key == $targetKey) return [$index,  static::countFields($fields)];
            if(!empty($fields)){
                $targetSearch = self::getPositionOfField($targetKey, $fields, $index);
                if(!is_null($targetSearch)) return $targetSearch;
            }
        }
        return null;
    }

    private static function countFields(array $toCount): int {
        $count = 0;
        foreach ($toCount as $key => $fields) $count += 1 + self::countFields($fields);
        return $count;
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




    /*private static function setArrayExistingRecordFromArrayData(Collection &$customFieldsOld, array $state,  array&$statedRecords): void {
        foreach ($state as $key => $fieldData){
            if(!empty($fieldData["id"])){
                $record = $customFieldsOld->firstWhere("id", $fieldData["id"]);
                if(!is_null($record)) $statedRecords[$key]= $record;
            }

            if(empty($fieldData["custom_fields"])) continue;
            self::setArrayExistingRecordFromArrayData($customFieldsOld, $fieldData["custom_fields"], $statedRecords);
        }
    }

    private static function saveCustomFieldFromData (int  $itemOrderRaw, array $itemInformation, CustomForm $customForm, HasMany $relationship, array &$existingRecords) {
        $itemOrder = $itemOrderRaw;
        foreach ($itemInformation as $itemKey => $itemData) {

            $itemData["custom_form_id"] = $customForm->id;
            $itemData["form_position"] = $itemOrder;
            $itemOrder++;

            //For the Layouts
            if(!empty($itemData["custom_fields"])){
                $itemOrder = self::saveCustomFieldFromData($itemOrder, $itemData["custom_fields"], $customForm,$relationship,$existingRecords);
                unset($itemData["custom_fields"]);
                $itemData["layout_end_position"] = $itemOrder-1;
            }
            else if(CustomFieldUtils::getFieldTypeFromRawDate($itemData) instanceof CustomLayoutType){
                unset($itemData["custom_fields"]);
                $itemData["layout_end_position"] = $itemOrder-1;
            }
            else $itemData["layout_end_position"] = null;

            /**@var CustomField $record*/ /*
            $record = ($existingRecords[$itemKey] ?? new CustomField());
            self::updateCustomField($record, $itemData);
            self::updateFieldRules($record,$itemData);
        }
        return $itemOrder;
    }


    //Copied from Repeaters and edited
/* public static function saveCustomFields(Repeater $component, CustomForm $customForm, array $state): void {

    $relationship = $customForm->customFields();

    $existingRecords = $customForm->customFields;
    $statedRecords = [];
    self::setArrayExistingRecordFromArrayData($customForm->customFields, $state,$statedRecords);

    //ToDo Modify CustomField in CustomField

    $recordsToDelete = [];

    foreach (collect($existingRecords)->pluck($relationship->getRelated()->getKeyName()) as $keyToCheckForDeletion) { //ToDo Make
        if (array_key_exists("record-$keyToCheckForDeletion", $statedRecords)) continue;
        $recordsToDelete[] = $keyToCheckForDeletion;
    }



    $childComponentContainers = $component->getChildComponentContainers();
    foreach ($childComponentContainers as $itemKey => $item) {
        // Perform some operation on $value here
        $childComponentContainers[$itemKey] =$item->getRawState();
    }

    self::saveCustomFieldFromData(1,$childComponentContainers,$customForm, $relationship,$statedRecords);

    $relationship
        ->whereKey($recordsToDelete)
        ->get()
        ->each(static function(CustomField $record){
            $record->delete();
            $record->getType()->afterEditFieldDelete($record);
        });
}

private static function updateCustomField(CustomField &$customField,array $itemData): void {
    $type = CustomFieldUtils::getFieldTypeFromRawDate($itemData);

    $rawData = $itemData;
    $customField->fill($itemData);
    $customFieldData = self::mutateOptionData($type, $customField, $itemData);

    //Check if something hase Change
    $customField->fill($customFieldData);
    if(!$customField->exists||$customField->isDirty()) $customField->save();
    $type->afterEditFieldSave($customField, $rawData);

}

public static function getGeneralFieldRepeaterValidationRule():Closure {
    return fn (CustomForm $record) =>
    function (string $attribute, $value, Closure $fail) use($record)  {
        $formIdentifier = $record->custom_form_identifier;
        $requiredGeneralFieldForm = GeneralFieldForm::query()
            ->where("custom_form_identifier", $formIdentifier)
            ->select("general_field_id")
            ->where("is_required", true)
            ->with("generalField")
            ->get();

        $requiredGeneralIDs = $requiredGeneralFieldForm
            ->map(fn ($fieldForm) => $fieldForm->general_field_id);

        $usedGeneralIDs =CustomFormEditorHelper::getUsedGeneralFieldIds($value);
        $notAddedRequiredFields = $requiredGeneralIDs
            ->filter(fn($id)=> !in_array($id, $usedGeneralIDs));

        if($notAddedRequiredFields->count() == 0) return;

        $fieldName = $requiredGeneralFieldForm
            ->filter(function($fieldForm) use ($notAddedRequiredFields) {
                $generalFieldId = $fieldForm->general_field_id;
                $notAddedField = $notAddedRequiredFields->first();
                return $generalFieldId == $notAddedField;
            })
            ->first()->generalField->name_de;

        $failureMessage =
            "Du must das generelle Feld \"" . $fieldName . "\" hinzufügen"; //ToDo Translate

        $fail($failureMessage);
    };
}

    private static function mutateOptionData(?CustomFieldType $type, CustomField $customField, array $customFieldData): array {
        if(!array_key_exists("options",$customFieldData)) return $customFieldData;
        $options = $customFieldData["options"];
        if(is_null($options)) $options = [];
        foreach ($type->getExtraTypeOptions() as $name => $option ){
            /**@var TypeOption $option */ /*
            $data = null;
            if(array_key_exists($name,$options)) $data = $options[$name];
            if($customField->exists) $options[$name] = $option->mutateOnSave($data, $customField);
            else $options[$name] = $option->mutateOnCreate($data,$customField);
        }
        $customFieldData["options"] = $options;
        return $customFieldData;
    }

    private static function updateFieldRules(CustomField $customField, array $customFieldData): void { //ToDo make setting which is running first
        if(!array_key_exists("rules",$customFieldData)) $rules = [];
        else $rules = $customFieldData["rules"];

        $existingIds = [];
        $executionOrder = 0;
        $toCreate = [];
        $toUpdate = [];
        foreach ($rules as $ruleData){
            $executionOrder += 1;
            $ruleData["execution_order"] = $executionOrder;

            if(array_key_exists("id",$ruleData)) $rule = $customField->fieldRules->where("id", $ruleData["id"])->first();
            else $rule = null;

            if(is_null($rule)) {
                $rule = new FieldRule();
                $rule->custom_field_id = $customField->id;
                $rule->fill($ruleData);
            }
            $ruleData = $rule->getAnchorType()->mutateDataBeforeSaveInEdit($ruleData, $rule);
            $ruleData = $rule->getRuleType()->mutateDataBeforeSaveInEdit($ruleData, $rule);

            //dd(json_encode($ruleData["rule_data"]));

            $rule->fill($ruleData);
            if(!$rule->exists || $rule->isDirty()) $rule->save();

            $cleanedData = $rule->toArray();
            unset($cleanedData["created_at"]);
            unset($cleanedData["updated_at"]);

            $cleanedData["anchor_data"] = json_encode($ruleData["anchor_data"]); //ToDO Change is mist
            $cleanedData["rule_data"] = json_encode($ruleData["rule_data"]);

            if(!$rule->exists) $toCreate[] = $cleanedData;
            else {
                $existingIds[] = $cleanedData["id"];
                if($rule->isDirty()) $toUpdate[] = $cleanedData;
            }

        }
        FieldRule::query()->upsert($toUpdate, ["id"]); //ToDo Optimize
        $customField->fieldRules()->whereNotIn("id",$existingIds)->delete();
        $customField->fieldRules()->createMany($toCreate);
    } */

}
