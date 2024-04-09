<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

abstract class RepeaterFieldAction
{
    public abstract function getAction(CustomForm $record,array $typeClosers): Action;

    protected function isVisible(CustomForm $record, Get $get, array $typeClosers, array $state, array $arguments): bool {
        foreach ($typeClosers as $closer) if($closer($record, $get,$state, $arguments)) return true;
        return false;
    }


    public static function getDefaultTypeClosure(CustomFieldType $type): Closure {
        return function (CustomForm $form,Get $get, $state,$arguments) use ($type):bool {
            $item = $state[$arguments["item"]];
            if (empty($item["general_field_id"])) return $item["type"] == $type::getFieldIdentifier();
            $gen = GeneralField::cached($item["general_field_id"]);
            return $gen->type == $type::getFieldIdentifier();
        };
    }

}
