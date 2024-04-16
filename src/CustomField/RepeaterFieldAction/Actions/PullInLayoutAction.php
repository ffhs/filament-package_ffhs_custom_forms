<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\NestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class PullInLayoutAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullIn")
            ->visible(function ($get, array $state, array $arguments) use ($typeClosers, $record) {
                if (!$this->isVisibleClosure($record, $typeClosers)($get, $state,$arguments))
                    return false;

                $type = $this->getUpperType($state,$arguments);
                if(is_null($type)) return false;
                return $type instanceof CustomLayoutType && !($type instanceof NestLayoutType) && !($type instanceof EggLayoutType);
            })
           // ->visible($this->isVisibleClosure($record,$typeClosers))
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

    private function getUpperType($state,$arguments): ?CustomFieldType{
        $itemIndex = $arguments["item"];
        $itemIndexPostion = PullInLayoutAction::getKeyPosition($itemIndex, $state);
        if ($itemIndexPostion == 0) return null;
        $upperCustomFieldData = $state[array_keys($state)[$itemIndexPostion - 1]];
        return CustomFormEditorHelper::getFieldTypeFromRawDate($upperCustomFieldData);
    }

    public static function getKeyPosition($key, $array): int {
        //Position in Repeater
        $keys = array_keys($array);
        return array_search($key, $keys);
    }


}
