<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;

class DefaultCustomFieldEditAction extends Action
{
    protected function setUp(): void {

       parent::setUp();

       $this->iconButton();
       $this->icon('carbon-settings-edit');

       $this->closeModalByClickingAway(false);
       $this->form([
          CustomFieldEditModal::make()
       ]);

       $this->fillForm(fn($state, $arguments) => EditCustomFormHelper::getFieldData($state, $arguments['item']));
       $this->mutateFormDataUsing(function(Action $action, ActionContainer $component){
           $forms = array_values($action->getLivewire()->getCachedForms());
           $form = $forms[sizeof($forms) - 1];
           $state = $form->getRawState();
           unset($state["key"]);
           return $state;
       });
       $this->action(function($set, $data, $arguments) {
           $key = $arguments['item'];
           $set("data.$key", $data);
       });


        /*$this->modalWidth(function(array $state, array $arguments){
                return CustomFieldEditModal::getEditCustomFormActionModalWith($state[$arguments["item"]]);
            });*/


       $this->modalHeading(function ($get, array $arguments) {
           $key =$arguments["item"];
           $data = $get("data.$key");
           $suffix = " - Feld bearbeiten "; //ToDo Translate
           if (empty($data["general_field_id"])) return $data["name"][app()->getLocale()] . $suffix;
           else return "G. ".GeneralField::cached($data["general_field_id"])->name. $suffix;
       });
    }


}
