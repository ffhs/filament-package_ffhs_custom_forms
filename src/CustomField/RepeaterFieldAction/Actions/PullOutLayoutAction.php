<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class PullOutLayoutAction extends RepeaterFieldAction
{

    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullOut")
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->icon('heroicon-m-arrow-long-left')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $newUpperState = $get("../../custom_fields");

                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("../../custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);
            })
            ->hidden(function ($arguments, $state, $get) {
                return is_null($get("../../custom_fields"));
            });
    }

}
