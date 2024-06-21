<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomNestLayoutType;
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
                //if(is_null($type)) return false;
                return $type instanceof CustomLayoutType && !($type instanceof CustomNestLayoutType) && !($type instanceof CustomEggLayoutType);
            })
           // ->visible($this->isVisibleClosure($record,$typeClosers))
            ->icon('heroicon-m-arrow-long-up')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $upperKey = $this->getUpperKey($itemIndex,$state);

                $newUpperState = $get("custom_fields.$upperKey.custom_fields");
                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("custom_fields.$upperKey.custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);

            });
    }

    protected function getUpperType($state,$arguments): ?CustomFieldType{
        $itemIndex = $arguments["item"];
        $itemIndexPostion = $this->getKeyPosition($itemIndex, $state);
        if ($itemIndexPostion == 0) return null;
        $upperCustomFieldData = $state[array_keys($state)[$itemIndexPostion - 1]];
        return CustomFieldUtils::getFieldTypeFromRawDate($upperCustomFieldData);
    }

    protected function getKeyPosition($key, $array): int {
        //Position in Repeater
        $keys = array_keys($array);
        return array_search($key, $keys);
    }



    function getUpperKey(mixed $itemIndex, array $state): string|int {
        $itemIndexPostion = $this->getKeyPosition($itemIndex, $state);
        return array_keys($state)[$itemIndexPostion - 1];
    }

}
