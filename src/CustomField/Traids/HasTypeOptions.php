<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

trait HasTypeOptions
{

    public function getExtraOptionFields():array{
        return [
            "customOptions" => [],
        ];
    }


    public function getExtraOptionSchema():?array{
        return [
            Group::make(function ($get){
                if(!empty($get("../../../general_filed_id"))) return [];
                return [$this->getCustomOptionsRepeater()];
            })
        ];
    }

    public function isGeneralExtraFieldPathSet(): bool {
        return false;
    }
    public function getGeneralExtraField(): ?array {
        return [
            $this->getCustomOptionsRepeater(true)
        ];
    }

    protected function getCustomOptionsRepeater (bool $withIdentifikator = false): Repeater {
        return Repeater::make("customOptions")
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
                    ->visible($withIdentifikator)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public function getCustomOptionsSelector ():Component {
        return  Select::make("available_options")
                ->label("Mögliche Auswahlmöglichkeiten")
                ->columnSpanFull()
                ->multiple()
                ->options(function($get){
                    $generalField = GeneralField::cached($get("../../../../general_field_id"));
                    return $generalField->customOptions->pluck("name_de","id"); //toDo Translate;
                })
            ;
    }

    public function mutateVariationDataBeforeFill(array $data):array{
        $data = parent::mutateVariationDataBeforeFill($data);
        if(empty($data["id"]))return $data;
        $customOptionDatas = [];
        /** @var CustomFieldVariation $customFieldVariation */
        $customFieldVariation = CustomFieldVariation::query()->with("customOptions")->firstWhere("id", $data["id"]);
        $customOptions = $customFieldVariation->customOptions;

        foreach ($customOptions as $option)$customOptionDatas["record-".$option->id]=$option->toArray();


        $data["options"]["customOptions"] = $customOptionDatas;

        return $data;
    }

    public function mutateVariationDataBeforeSave(array $data):array{
        $data = parent::mutateVariationDataBeforeSave($data);
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
            if(is_null($option))dd($option,$optionExist,$customOptions,$optionData);
            $option->fill($optionData)->save();
            if(!$optionExist)$variation->customOptions()->attach($option);

        }
    }

    public static function prepareCloneOptions(array $templateOptions, bool $isInheritGeneral) :array{
        if($isInheritGeneral) return $templateOptions;
        foreach($templateOptions["customOptions"] as $key => $option){
            unset($templateOptions["customOptions"][$key]["id"]);
        }
        return $templateOptions;
    }

}
