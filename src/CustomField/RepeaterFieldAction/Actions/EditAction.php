<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class EditAction extends RepeaterFieldAction
{

    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make('edit')
            ->action(fn($get, $set, $data, $arguments) => CustomFormEditorHelper::setCustomFieldInRepeater($data, $get, $set, $arguments))
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->mutateFormDataUsing(fn(Action $action)=> CustomFormEditorHelper::getRawStateForm($action,1))
            ->fillForm(fn($state, $arguments) => $state[$arguments["item"]])
            ->closeModalByClickingAway(false)
            ->icon('heroicon-m-pencil-square')
            ->label("Bearbeiten") //ToDo Translate
            ->modalWidth(function(array $state, array $arguments){
                return CustomFieldEditModal::getEditCustomFormActionModalWith($state[$arguments["item"]]);
            })
            ->form(function(Get $get, $state, array $arguments) use ($record) : array {
                return [CustomFieldEditModal::make($record,$state[$arguments["item"]])];
            })
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $suffix = " - Felddaten bearbeiten ";
                if (empty($data["general_field_id"])) return $data["name_de"] . $suffix; //ToDo Translate
                else return "G. ".GeneralField::cached($data["general_field_id"])->name_de. $suffix; //ToDo Translate
            });
    }

}
