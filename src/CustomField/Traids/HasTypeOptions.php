<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

trait HasTypeOptions
{

    public function getExtraOptionFields(?GeneralField $generalField = null):array{
        return [
            "customOptions" => [],
        ];
    }


    public function getExtraOptionSchema(?GeneralField $generalField = null):?array{
        if(!is_null($generalField)) return [];
        return [
            $this->getCustomOptionsRepeater(true),
        ];
    }

    public function isGeneralExtraFieldPathSet(): bool {
        return false;
    }
    public function getGeneralExtraField(): ?array {
        return [
            $this->getCustomOptionsRepeater(true),
        ];
    }

    protected function getCustomOptionsRepeater (bool $isGeneralField ): Repeater {
        $repeater =  Repeater::make("customOptions");

        if($isGeneralField) $repeater->relationship("customOptions");

        $repeater
            ->collapseAllAction(fn($action) => $action->hidden())
            ->expandAllAction(fn($action) => $action->hidden())
            ->reorderableWithDragAndDrop(false) //ToDo
            ->itemLabel(fn($state)=> $state["name_de"]) //ToDo Translate
            ->label("Feldoptionen") //ToDo Translate
            ->columnSpanFull()
            ->collapsible()
            ->collapsed()
            ->addable()
            ->columns()
            ->schema([
                TextInput::make("name_de")->label("Name De")->required(),
                TextInput::make("name_en")->label("Name En")->required(),
                TextInput::make("identifier")
                    ->label("Identifikator")
                    ->visible($isGeneralField)
                    ->columnSpanFull()
                    ->required(),
            ]);

        return $repeater;
    }

    public function getCustomOptionsSelector (?GeneralField $generalField = null):Component {
        return  Select::make("available_options")
                ->options(function($state){

                })
                ->multiple()
            ;
    }

    public function mutateVariationDataBeforeFill(array $data, ?GeneralField $generalField = null ):array{
        $data = parent::mutateVariationDataBeforeFill($data, $generalField);
        if(empty($data["id"]))return $data;
        $customOptionDatas = [];
        /** @var CustomFieldVariation $customFieldVariation */
        $customFieldVariation = CustomFieldVariation::query()->with("customOptions")->firstWhere("id", $data["id"]);
        $customOptions = $customFieldVariation->customOptions;

        foreach ($customOptions as $option)$customOptionDatas["record-".$option->id]=$option->toArray();


        $data["options"]["customOptions"] = $customOptionDatas;

        return $data;
    }

    public function mutateVariationDataBeforeSave(array $data, ?GeneralField $generalField = null):array{
        $data = parent::mutateVariationDataBeforeSave($data, $generalField);
        if(empty($data["options"])) $data["options"] = [];
        if(empty($data["options"]["customOptions"])) $data["options"]["customOptions"] = [];
        $data["customOptions"] = $data["options"]["customOptions"];
        unset($data["options"]["customOptions"]);
        return $data;
    }

    public function afterCustomFieldVariationSave(?CustomFieldVariation $variation, array $variationData):void {
        $customOptions = $variation->customOptions;
        if(empty($variationData["customOptions"])) {
            $customOptions->each(fn(CustomOption $option) =>$option->delete());
            return;
        }

        $customOptionDatas = collect($variationData["customOptions"]);
        $changedIds = $customOptionDatas
            ->filter(fn(array $option) => !empty($option["id"]))
            ->map(fn(array $option) =>$option["id"]);
        $customOptions
            ->filter(fn(CustomOption $option) => !$changedIds->contains($option->id))
            ->each(fn(CustomOption $option) =>$option->delete());

        foreach ($customOptionDatas as $optionData){
            $optionExist = !empty($optionData["id"]);
            /**@var CustomOption $option*/
            if($optionExist) $option = $customOptions->firstWhere("id",$optionData["id"]);
            else $option = new CustomOption();
            $option->fill($optionData)->save();
            if(!$optionExist)$variation->customOptions()->attach($option);

        }
    }

}
