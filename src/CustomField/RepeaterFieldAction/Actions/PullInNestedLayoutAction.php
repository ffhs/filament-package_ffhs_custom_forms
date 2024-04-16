<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class PullInNestedLayoutAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullInEgg")
            ->visible($this->isVisibleClosure($record,$typeClosers))
            ->icon('heroicon-m-arrow-long-up')
            ->label("In Layout verschieben")
            ->form(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $upperKey = $this->getUpperKey($itemIndex, $state);

                $upperData = $get("custom_fields.$upperKey");
                $eggs = $upperData["custom_fields"];

                $options = [];
                foreach ($eggs as $key => $egg) $options[$key] = $egg["name_de"];//ToDo Translate

                return [
                  Select::make("egg_key")
                    ->label("Ei auswählen") //ToDo Translate
                    ->options($options)
                    ->required()
                ];
            })

            ->action(function (array $arguments, array $state, $set, Get $get, array $data) {
                $itemIndex = $arguments["item"];
                $upperKey = $this->getUpperKey($itemIndex, $state);

                $path = "custom_fields.$upperKey.custom_fields.".$data["egg_key"].".custom_fields";

                $newUpperState = $get($path);
                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set($path, $newUpperState);

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

    /**
     * @param  mixed  $itemIndex
     * @param  array  $state
     * @return int|string
     */
    function getUpperKey(mixed $itemIndex, array $state): string|int {
        $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
        return array_keys($state)[$itemIndexPostion - 1];
    }


}
