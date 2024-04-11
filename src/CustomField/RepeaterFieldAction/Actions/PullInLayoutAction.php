<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class PullInLayoutAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullIn")
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->icon('heroicon-m-arrow-long-up')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                $upperKey = array_keys($state)[$itemIndexPostion - 1];

                $newUpperState = $get("custom_fields.$upperKey.custom_fields");
                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("custom_fields.$upperKey.custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);

            });
    }

    public static function getKeyPosition($key, $array): int {
        //Position in Repeater
        $keys = array_keys($array);
        return array_search($key, $keys);
    }


}
