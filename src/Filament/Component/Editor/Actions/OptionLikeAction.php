<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\EditTypeOptionModal;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;

abstract class OptionLikeAction extends Action
{
    protected function setUp(): void {

        parent::setUp();

        $this->iconButton();

        $this->closeModalByClickingAway(false);

        $this->action(function($set, $data, $arguments) {
            $key = $arguments['item'];
            $set($key, $data);
        });
        $this->fillForm(fn($state, $arguments) => $state[$arguments['item']]);

        $this->mutateFormDataUsing(function(Action $action){
            $forms = array_values($action->getLivewire()->getCachedForms());
            $form = $forms[sizeof($forms) - 1];
            $state = $form->getRawState();
            unset($state["key"]);
            return $state;
        });

        $this->modalHeading(function ($get, array $arguments) {
            $key =$arguments["item"];
            $data = $get($key);
            $suffix = " - ". $this->getTitleName() ." bearbeiten "; //ToDo Translate
            if (empty($data["general_field_id"])) return ($data["name"][app()->getLocale()]??"") . $suffix;
            else return "G. ".GeneralField::cached($data["general_field_id"])->name. $suffix;
        });


    }

    protected abstract function getTitleName(): string;
}
