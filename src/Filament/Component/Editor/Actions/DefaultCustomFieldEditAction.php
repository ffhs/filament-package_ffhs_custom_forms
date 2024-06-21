<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;

class DefaultCustomFieldEditAction extends Action
{
    protected function setUp(): void {

       parent::setUp();

       $this->iconButton();
       $this->icon('carbon-settings-edit');



       #$this->action(fn($get, $set, $data, $arguments) => CustomFormEditorHelper::setCustomFieldInRepeater($data, $get, $set, $arguments));
       #$this->mutateFormDataUsing(fn(Action $action)=> CustomFormEditorHelper::getRawStateForm($action,1));

       $this->closeModalByClickingAway(false);

       $this->action(fn($state, $arguments) =>CustomFormEditorHelper::getFieldData($state, $arguments['item']));
       $this->fillForm(fn($state, $arguments) => CustomFormEditorHelper::getFieldData($state,$arguments['item']));
      #$this->form([TextInput::make("")]);


       /*$this->modalWidth(function(array $state, array $arguments){
               return CustomFieldEditModal::getEditCustomFormActionModalWith($state[$arguments["item"]]);
           });
       $this->form(function(Get $get, $state, array $arguments,$record) : array {
               return [CustomFieldEditModal::make($record,$state[$arguments["item"]])];
           });*/


     /*  $this->modalHeading(function (array $state, array $arguments) {
               $data = $state[$arguments["item"]];
               $suffix = " - Felddaten bearbeiten ";
               if (empty($data["general_field_id"])) return $data["name_de"] . $suffix; //ToDo Translate
               else return "G. ".GeneralField::cached($data["general_field_id"])->name_de. $suffix; //ToDo Translate
           });*/
    }


}
