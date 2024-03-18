<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

trait HasTypeOptions
{

    public function getExtraTypeOptionComponents(): array{
        return array_merge(parent::getExtraTypeOptionComponents(),
            [
                Group::make()
                    ->schema(fn($get)=> is_null($get("../general_field_id"))? []:[$this->getCustomOptionsSelector()])
                    ->hidden(fn($get)=> is_null($get("../general_field_id")))
                    ->columnSpanFull(),
                Group::make()
                    ->schema(fn($get)=> is_null($get("../general_field_id"))? [$this->getCustomOptionsRepeater()]:[])
                    ->visible(fn($get)=> is_null($get("../general_field_id")))
                    ->columnSpanFull(),
            ]
        );
    }


    public function getExtraGeneralTypeOptionComponents(): array {
        return array_merge(
            parent::getExtraGeneralTypeOptionComponents(),
            [$this->getCustomOptionsRepeater(true)]
        );
    }


    public function hasExtraGeneralTypeOptions(): bool {
        return true;
    }


    protected function getCustomOptionsRepeater (bool $withIdentifikator = false): Repeater {
        $repeater = Repeater::make("customOptions")
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

        if($withIdentifikator) $repeater->relationship("customOptions");

        return $repeater;
    }

    public function getCustomOptionsSelector ():Component {
        return  Select::make("available_options")
                ->label("Mögliche Auswahlmöglichkeiten")
                ->columnSpanFull()
                ->multiple()
                ->options(function($get){
                    $generalField = GeneralField::cached($get("../general_field_id"));
                    return $generalField->customOptions->pluck("name_de","identifier"); //toDo Translate;
                });
    }


    public function mutateVariationDataBeforeFillOLD(array $data):array{
        $data = parent::mutateVariationDataBeforeFill($data);
        if(empty($data["id"]))return $data;
        if(!empty($data["general_field_id"])) return $data;
        $customOptionDatas = [];
        /** @var CustomFieldVariation $customFieldVariation */
        $customFieldVariation = CustomFieldVariation::query()->with("customOptions")->firstWhere("id", $data["id"]);
        $customOptions = $customFieldVariation->customOptions;

        foreach ($customOptions as $option)$customOptionDatas["record-".$option->id]=$option->toArray();


        $data["customOptions"] = $customOptionDatas;

        return $data;
    }


    public function afterCustomFieldVariationSaveOLD(?CustomFieldVariation $variation, array $variationData):void {
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

    public static function prepareCloneOptionsOLD(array $variationData, string $target, $set, Get $get) :array{
        if(!empty($get("general_field_id"))) return $variationData["options"];

        $customOptions = $variationData["customOptions"] ;
        foreach($customOptions as $key => $option) unset($customOptions[$key]["id"]);

        $set($target.".customOptions",$customOptions);
        return $variationData["options"];
    }
    public function getAvailableCustomOptionsOLD(CustomFieldVariation $record) : \Illuminate\Support\Collection{
        if($record->customField->isInheritFromGeneralField()) {
            $options = $record
                ->customField
                ->generalField //ToDo Optimize generalFields
                ->customOptions
                ->whereIn("identifier", $this->getOptionParameter($record, "available_options"));
        }
        else  $options = $record->customOptions;
        return $options->pluck("name_de","identifier");//ToDo Translate
    }

    public function getAllCustomOptionsOLD(CustomFieldVariation|CustomFieldAnswer $record) : \Illuminate\Support\Collection{
        if($record instanceof CustomFieldAnswer) $record = $record->customFieldVariation;
        if($record->customField->isInheritFromGeneralField()) $options = $record->customField->generalField->customOptions;
        else $options = $record->customOptions;
        return $options->pluck("name_de","identifier");//ToDo Translate
    }



    /*public function getExtraOptionSchema():?array{
        return [
            Group::make()
                ->statePath("options")
                ->columnSpanFull()
                ->columns()
                ->schema([
                    Group::make($this->getExtraOptionSchemaHasOptions())->columnSpanFull()->columns(),
                    Group::make()
                        ->hidden(fn($get)=> is_null($get("../../../general_field_id")))
                        ->columnSpanFull()
                        ->schema(function ($get){
                            if(!is_null($get("../../../general_field_id"))) return [$this->getCustomOptionsSelector()];
                            return [];
                        }),
                ])->hidden(fn ($get)=>is_null($get("../../general_field_id")) && empty($this->getExtraOptionSchemaHasOptions())),
            Group::make()
                ->columnSpanFull()
                ->schema(function ($get){
                    if(is_null($get("../../general_field_id"))) return [$this->getCustomOptionsRepeater()];
                    return [];
                }),
        ];
    }*/
    /*public function getExtraOptionsComponent(): ?Component{
        if(!$this->hasExtraOptions()) return null;
        return Section::make()
            ->schema($this->getExtraOptionSchema())
            ->columns();
    }*/
}
