<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CustomFormEditSave
{
    private static function updateCustomField(CustomForm $customForm,CustomField $customfield,array $itemData): void {
        $customFieldData = array_filter($itemData, fn($key) =>!str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);
        $variations = array_filter($itemData, fn($key) => str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);

        $type = CustomFormEditForm::getFieldTypeFromRawDate($itemData);

        if($customfield->exists) $customFieldData = $type->mutateCustomFieldDataBeforeSave($customFieldData);
        else $customFieldData = $type->mutateCustomFieldDataBeforeCreate($customFieldData);
        $customfield->fill($customFieldData)->save();



        if(empty($variations)) return; //If it is empty, it has also no Template variation what mean it wasn't edit

        $variationsOld = $customfield->customFieldVariation;
        $updatetVariationIds = [];

        $formConfiguration = $customForm->getFormConfiguration();

        foreach($variations as $variationName => $variationData){
            $variationData = array_values($variationData)[0];
            $variationId = explode("variation-",$variationName)[1];
            if($variationId == "") $variationId = null;
            else $variationId = intval($variationId);

            /** @var CustomFieldVariation|null $variation */
            $variation = $variationsOld
                ->filter(fn(CustomFieldVariation $fieldVariation)=> $fieldVariation->variation_id == $variationId)
                ->first();

            if($variation == null){
                //Prepare Variation Data before Create
                $variationData = $type->mutateVariationDataBeforeCreate($variationData);
                //Create new Variation
                $variation = new CustomFieldVariation();

                $variation->variation_id = $variationId;
                $variation->variation_type = $formConfiguration::variationModel();
                $variation->custom_field_id = $customfield->id;
            }else{
                //Prepare Variation Data
                $variationData = $type->mutateVariationDataBeforeSave($variationData);
            }

            $variation->fill($variationData);
            $variation->save();

            $updatetVariationIds[] = $variationId;
        }

        //Delete the deleted Variation
        $variationsOld
            ->filter(fn(CustomFieldVariation $variation)=>!in_array($variation->variation_id,$updatetVariationIds))
            ->each(fn(CustomFieldVariation $variation)=>$variation->delete());


    }


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

    //Copied from Repeaters and edited
    public static function saveCustomFields(Repeater $component, CustomForm $customForm, array $state): void {


        $relationship = $customForm->customFields();

        $existingRecords = $customForm->customFields;
        $statedRecords = [];
        self::setArrayExistingRecordFromArrayData($customForm->customFields, $state,$statedRecords);

        //ToDo Modify CustomField in CustomField

        $recordsToDelete = [];

        foreach (collect($existingRecords)->pluck($relationship->getRelated()->getKeyName()) as $keyToCheckForDeletion) { //ToDo Make
            if (array_key_exists("record-$keyToCheckForDeletion", $statedRecords)) {
                continue;
            }
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
            self::updateCustomField($customForm,$record, $itemData);

        }
        return $itemOrder;
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
}
