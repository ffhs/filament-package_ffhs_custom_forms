<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\NestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class PullOutLayoutAction extends RepeaterFieldAction
{

    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullOut")
            ->visible(function ($get, array $state, array $arguments) use ($typeClosers, $record) {
                if(!$this->isVisibleClosure($record,$typeClosers)($get,$state,$arguments) || is_null($get("../../custom_fields")))
                    return false;
                $typeKey = $get("../../type");
                if(is_null($typeKey)) return true;
                $type = CustomFieldType::getTypeFromName($typeKey);
                if(is_null($type)) return true;
                return !($type instanceof EggLayoutType || $type instanceof NestLayoutType);
            })
            ->label("Aus Layout herausnehmen") //ToDo translate
            ->icon('heroicon-m-arrow-long-left')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $newUpperState = $get("../../custom_fields");

                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("../../custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);
            });
    }


}
