<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CustomFormEditSave
{

    private static function setArrayExistingRecordFromArrayData(Collection &$customFieldsOld, array $state,  array&$statedRecords): void {
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
            else if(CustomFormEditForm::getFieldTypeFromRawDate($itemData) instanceof CustomLayoutType){
                unset($itemData["custom_fields"]);
                $itemData["layout_end_position"] = $itemOrder-1;
            }
            else $itemData["layout_end_position"] = null;

            /**@var CustomField $record*/
            $record = ($existingRecords[$itemKey] ?? new CustomField());
            self::updateCustomField($record, $itemData);
            self::updateFieldRules($record,$itemData);
        }
        return $itemOrder;
    }


    //Copied from Repeaters and edited
    public static function saveCustomFields(Repeater $component, CustomForm $customForm, array $state): void {

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

        $relationship
            ->whereKey($recordsToDelete)
            ->get()
            ->each(static fn(Model $record) =>  $record->delete());

        $childComponentContainers = $component->getChildComponentContainers();
        foreach ($childComponentContainers as $itemKey => $item) {
            // Perform some operation on $value here
            $childComponentContainers[$itemKey] =$item->getRawState();
        }

        self::saveCustomFieldFromData(1,$childComponentContainers,$customForm, $relationship,$statedRecords);

    }

    private static function updateCustomField(CustomField &$customField,array $itemData): void {
        $type = CustomFormEditForm::getFieldTypeFromRawDate($itemData);

        $rawData = $itemData;
        $customField->fill($itemData);
        $customFieldData = self::mutateOptionData($type, $customField, $itemData);

        //Check if something hase Change
        $customField->fill($customFieldData);
        if(!$customField->exists||$customField->isDirty()) $customField->save();
        $type->doAfterFieldSave($customField, $rawData);

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

                $usedGeneralIDs =CustomFormEditForm::getUsedGeneralFieldIds($value);
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
        foreach ($type->getExtraTypeOptions() as $name => $option ){
            /**@var TypeOption $option */
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
            else {
                $rule = new FieldRule();
                $rule->fill($ruleData);
            }
            $ruleData = $rule->getAnchorType()->mutateDataBeforeSaveInEdit($ruleData, $rule);
            $ruleData = $rule->getRuleType()->mutateDataBeforeSaveInEdit($ruleData, $rule);

            $rule->fill($ruleData);

            if(!$rule->exists) $toCreate[] = $rule->toArray();
            else {
                $existingIds[] = $ruleData["id"];
                if($rule->isDirty()) $toUpdate[] = $rule->toArray();
            }

        }
        FieldRule::query()->upsert($toUpdate, ["id"]);
        $customField->fieldRules()->whereNotIn("id",$existingIds)->delete();
        $customField->fieldRules()->createMany($toCreate);
    }


}
