<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

class TemplateDissolveAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make('dissolve')
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->closeModalByClickingAway(false)
            ->icon('carbon-sync-settings')
            ->color(Color::hex("#de9310"))
            ->label("Auflösen")//ToDo Translate
            ->requiresConfirmation()
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $template = CustomForm::cached($data["template_id"]);
                $name = $template->short_title;

                return "Möchten sie Wirklich das Template '" . $name . "'  auflösen?"; //ToDo Translate
            })
            ->action(function(Get $get, $set, array $state, array $arguments) use ($record) {
                $repeaterKey = $arguments["item"];
                $templateID = $state[$repeaterKey]["template_id"];
                $customFields = $get("custom_fields");

                $keyLocation = self::getKeyPosition($repeaterKey, $customFields);

                //Splitt the Fields
                $fieldsBeforeTemplate = array_slice($customFields, 0, $keyLocation,true);
                $fieldsAfterTemplate = array_diff_key($customFields, $fieldsBeforeTemplate);


                //Remove the Template Field
                unset($fieldsAfterTemplate[$repeaterKey]);

                //Get Template Fields
                $template = CustomForm::cached($templateID);
                $templateFields = $template->customFields;
                $templateFields = $templateFields->map(function (CustomField $field) use ($template, $record) {


                    //Clone Field
                    $fieldData = $field->toArray();

                    //Mutate Field Data's
                    $type = $field->getType();

                    //Load OptionData now, because it needs the field id
                    $fieldData = EditCustomFieldForm::mutateOptionData($fieldData, $template);
                    $fieldData = EditCustomFieldRule::mutateRuleDataOnLoad($fieldData, $template);

                    $fieldData = $type->mutateOnTemplateDissolve($fieldData, $field);

                    $fieldData = self::unsetAttributesForClone($fieldData);
                    $fieldData["custom_form_id"] = $record->id;

                    //Clone Ankers and Rules
                    $rules = $field->fieldRules;
                    $rulesCloned = [];
                    foreach ($fieldData["rules"] as $ruleData){

                        /**@var FieldRule $fieldRule*/
                        $fieldRule = $rules->where("id", $ruleData["id"])->first();

                        $ruleData = $fieldRule->toArray();

                        $ruleData = self::unsetAttributesForClone($ruleData);

                        $ruleData = $fieldRule->getAnchorType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);
                        $ruleData = $fieldRule->getRuleType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);

                        unset($ruleData["custom_field_id"]);

                        //Set for Repeaters
                        $rulesCloned[uniqid()] = $ruleData;
                    }
                    $fieldData["rules"] = $rulesCloned;

                    return $fieldData;
                })->toArray();

                //Add unique Keys to the field's array
                $middleFields = [];
                foreach ($templateFields as $field){
                    $middleFields[uniqid()] = $field;
                }

                //Place the new fields there were the template was
                $combinedFields = array_merge($fieldsBeforeTemplate,$middleFields,$fieldsAfterTemplate);

                //Set the fields back in the repeater
                $set("custom_fields",$combinedFields);
            });
    }

    private static function unsetAttributesForClone(array $data):array {
        unset($data["id"]);
        unset($data["created_at"]);
        unset($data["deleted_at"]);
        unset($data["updated_at"]);
        return $data;
    }

    private static function getKeyPosition($key, $array): int {
        //Position in Repeater
        $keys = array_keys($array);
        return array_search($key, $keys);
    }

}
