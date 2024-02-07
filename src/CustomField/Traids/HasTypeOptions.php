<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

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

    public function getGeneralExtraField(): ?array {
        return [
            $this->getCustomOptionsRepeater(true)
        ];
    }

    public function getExtraOptionsComponent(): ?Component{
        if(!$this->hasExtraOptions()) return null;
        return Section::make()
            ->schema($this->getExtraOptionSchema())
            ->columns();
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


        $data["customOptions"] = $customOptionDatas;

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

    public static function prepareCloneOptions(array $variationData, string $target, $set, Get $get) :array{
        if(!empty($get("general_field_id"))) return $variationData["options"];

        $customOptions = $variationData["customOptions"] ;
        foreach($customOptions as $key => $option) unset($customOptions[$key]["id"]);

        $set($target.".customOptions",$customOptions);
        return $variationData["options"];
    }

}
