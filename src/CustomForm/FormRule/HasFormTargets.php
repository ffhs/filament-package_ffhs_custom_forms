<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;

trait HasFormTargets
{

    public function getTargetsSelect(): Select{
        return Select::make('targets')
            ->multiple()
            ->label("Target")
            ->options(function ($get, CustomForm $record) {
                $fields = $get("../../../../../custom_fields");
                return collect($fields)->pluck("name.". $record->getLocale(),"identifier");

                //ToDo Templates etc

            })
            ->live();
    }

    public function getTargetSelect(): Select{
        return Select::make('target')
            ->label("Target")
            ->options(function ($get, CustomForm $record) {
                $fields = $get("../../../../../custom_fields");
                return collect($fields)->pluck("name.". $record->getLocale(),"identifier");

                //ToDo Templates etc

            })
            ->live();
    }

}
