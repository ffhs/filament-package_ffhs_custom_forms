<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

class DefaultTemplateDissolveAction extends Action
{


    protected function setUp(): void {

        parent::setUp();

        $this->iconButton();


        $this->closeModalByClickingAway(false)
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
            ->action(function(CustomForm $record, Get $get, $set, array $state, array $arguments, Component $component){
                $key = $arguments["item"];
                $templateID = $state[$key]["template_id"];
                $customFields = $state;

                $templatePos = $state[$key]["form_position"];

                //Deleting
                $customFields = EditCustomFormHelper::removeField($key, $customFields);

                //Get Template Fields
                $template = CustomForm::cached($templateID);
                $templateFormData = EditCustomFormLoadHelper::load($template);
                $templateFields = $templateFormData['custom_fields'];

                foreach ($templateFields as $key => $field)
                    $templateFields[$key] = $this->cloneTemplateField($field,$record);


                //Place the new fields there were the template was
                $customFields = EditCustomFormHelper::addMultipleFields($templateFields, $templatePos ,$customFields);


                //Set the fields back in the repeater
                $set($component->getStatePath(), $customFields, true);
            });

    }

    protected function cloneTemplateField($fieldData, CustomForm $targetForm):array {

        //Mutate Field Data's
        $field = $rules = CustomField::cached($fieldData["id"]);
        $type = CustomFieldUtils::getFieldTypeFromRawDate($fieldData);

        //Load OptionData now, because it needs the field id
      //  $fieldData = CustomFormEditorMutationHelper::mutateOptionData($fieldData, $template); //ToDO on Template Dissolve action
      //  $fieldData = CustomFormEditorMutationHelper::mutateRuleDataOnLoad($fieldData, $template); ToDO on Template Dissolve action

        // $fieldData = $type->mutateOnTemplateDissolve($fieldData, $field); //ToDo

        $fieldData = static::unsetAttributesForClone($fieldData);
        $fieldData["custom_form_id"] = $targetForm->id;

        //Clone Ankers and Rules
        $rules = $field->fieldRules;
        $rulesCloned = [];
        foreach ($fieldData["rules"] ?? [] as $ruleData){

            /**@var FieldRule $fieldRule*/
            $fieldRule = $rules->where("id", $ruleData["id"])->first();

            $ruleData = $fieldRule->toArray();

            $ruleData = static::unsetAttributesForClone($ruleData);

            $ruleData = $fieldRule->getAnchorType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);
            $ruleData = $fieldRule->getRuleType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);

            unset($ruleData["custom_field_id"]);

            //Set for Repeaters
            $rulesCloned[uniqid()] = $ruleData;
        }
        $fieldData["rules"] = $rulesCloned;

        return $fieldData;
    }

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()

    private static function unsetAttributesForClone(array $data):array {
        unset($data["id"]);
        unset($data["created_at"]);
        unset($data["deleted_at"]);
        unset($data["updated_at"]);
        return $data;
    }





}
