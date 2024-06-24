<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomNestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class PullInNestedLayoutAction extends PullInLayoutAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullInEgg")
            ->icon('heroicon-m-arrow-long-up')
            ->label("In Layout verschieben")
            ->visible(function ($get, array $state, array $arguments) use ($typeClosers, $record) {
                if(!$this->isVisibleClosure($record,$typeClosers)($get,$state,$arguments)) return false;
                return $this->getUpperType($state,$arguments) instanceof CustomNestLayoutType;
            })
            ->form(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $upperKey = $this->getUpperKey($itemIndex, $state);

                $upperData = $get("custom_fields.$upperKey");
                $eggs = $upperData["custom_fields"];

                $options = [];
                foreach ($eggs as $key => $egg) $options[$key] = $egg["name_de"];//ToDo Translate;
                $upperType = CustomFieldUtils::getFieldTypeFromRawDate($upperData);

                /**@var CustomNestLayoutType $upperType*/
                return [
                  Select::make("egg_key")
                    ->label($upperType->getEggType()->getTranslatedName()." auswählen") //ToDo Translate
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


}
