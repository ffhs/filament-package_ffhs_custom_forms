<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

trait HasTriggerEventFormTargets
{
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;

    protected array|null $cachedAllFieldsData = null;

    public function getTargetsSelect(): Select{
        return Select::make('targets')
            ->multiple()
            ->label("Target")
            ->options($this->getTargetOptions(...))
            ->lazy()
            ->hidden(function ($set, $get){
                //Fields with an array doesn't generate properly
                if($get('targets') == null)
                $set("targets",[]);
            })
            ->live();
    }

    public function getTargetSelect(): Select{
        return Select::make('target')
            ->label("Target")
            ->options($this->getTargetOptions(...))
            ->live();
    }


    public function getTargetOptions($get, $record): array
    {
        $fields = collect($this->getAllFieldsData($get))
            ->map(fn($fieldData) => (new CustomField())->fill($fieldData));
        return $this->getSelectOptionsFromFields($fields);
    }



    public function getTargetFieldData(Get $get):array|null {
        $identifier = $get("target");
        if(is_null($identifier)) return null;

        $fields = $this->getAllFieldsData($get);

        return $fields[$identifier];
    }


    public function getAllFieldsData(Get $get): array
    {
        if(!is_null($this->cachedAllFieldsData)) return $this->cachedAllFieldsData;
        $fields = $get("../../../../../custom_fields")??[] ;
        return $this->cachedAllFieldsData = $this->getFieldDataFromFormData($fields);
    }

}
