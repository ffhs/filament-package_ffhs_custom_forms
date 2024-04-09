<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFormFieldFunctions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

class EditAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make('edit')
            ->action(fn($get, $set, $data, $arguments) => EditCustomFormFieldFunctions::setCustomFieldInRepeater($data, $get, $set, $arguments))
            ->visible(fn($get, array $state, array $arguments)=> $this->isVisible($record,$get,$typeClosers,$state,$arguments))
            ->mutateFormDataUsing(fn(Action $action)=> EditCustomFieldAction::getRawStateActionForm($action))
            ->fillForm(fn($state, $arguments) => $state[$arguments["item"]])
            ->closeModalByClickingAway(false)
            ->icon('heroicon-m-pencil-square')
            ->label("Bearbeiten") //ToDo Translate
            ->modalWidth(function(array $state, array $arguments){
                return EditCustomFieldAction::getEditCustomFormActionModalWith($state[$arguments["item"]]);
            })
            ->form(function(Get $get, $state, array $arguments) use ($record) : array {
                    return EditCustomFieldForm::getCustomFieldSchema($state[$arguments["item"]], $record);
            })
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $suffix = " Felddaten bearbeiten ";
                if (empty($data["general_field_id"])) return $data["name_de"] . $suffix; //ToDo Translate
                else return "G. ".GeneralField::cached($data["general_field_id"])->name_de. $suffix; //ToDo Translate
            });
    }

}
