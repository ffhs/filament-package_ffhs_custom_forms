<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class NewEggActionComponent extends RepeaterFieldAction
{

    protected function getFillForm(array $state, array $arguments):array {
        $nestType = CustomFormEditorHelper::getFieldTypeFromRawDate($state[$arguments["item"]]);
        /**@var CustomNestLayoutType $nestType*/
        $eggType = $nestType->getEggType();

        return [
            "identify_key" => uniqid(),
            "type" => $eggType::getFieldIdentifier(),
            "options" => $eggType->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];
    }
    public function getAction(CustomForm $record, array $typeClosers): Action {

        return Action::make('add_egg')
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->action(function($get, $set, $data, $arguments) {
                $path = "custom_fields.".$arguments["item"].".custom_fields";
                $fields = $get("custom_fields.".$arguments["item"].".custom_fields");
                $fields[uniqid()] = $data;
                $set($path,$fields);
            })
            ->mutateFormDataUsing(fn(Action $action)=> CustomFormEditorHelper::getRawStateForm($action,1))
            ->fillForm(fn($state, $arguments)=> $this->getFillForm($state,$arguments))
            ->icon('carbon-add-alt')
            ->label(function($arguments, $state){
                /**@var CustomNestLayoutType $type  */
                $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state[$arguments["item"]]);
                return $type->getEggType()->getTranslatedName() . " hinzufügen"; //ToDo Translate
            })
            ->closeModalByClickingAway(false)
            ->modalWidth(function(array $state, array $arguments){
                return CustomFieldEditModal::getEditCustomFormActionModalWith($this->getFillForm($state,$arguments));
            })
            ->form(function(Get $get, $state, array $arguments) use ($record) : array {
                return [CustomFieldEditModal::make($record,$this->getFillForm($state,$arguments))];
            })
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $suffix = " Felddaten bearbeiten ";
                if (empty($data["general_field_id"])) return $data["name_de"] . $suffix; //ToDo Translate
                else return "G. ".GeneralField::cached($data["general_field_id"])->name_de. $suffix; //ToDo Translate
            });
    }
}
