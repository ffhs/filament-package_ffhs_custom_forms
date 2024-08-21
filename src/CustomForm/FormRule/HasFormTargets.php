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
            ->options($this->getTargetOptions())
            ->live();
    }

    public function getTargetSelect(): Select{
        return Select::make('target')
            ->label("Target")
            ->options($this->getTargetOptions())
            ->live();
    }

    /**
     * @return \Closure
     */
    public function getTargetOptions(): \Closure
    {
        return function ($get, CustomForm $record) {
            $fields = $get("../../../../../custom_fields");
            $data = collect($fields)->pluck("name." . $record->getLocale(), "identifier")->toArray();

            foreach ($data as $key => $value)
                if (empty($value)) $data[$key] = "?";


            return $data;


            //ToDo Templates etc
        };
    }

}
