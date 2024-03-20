<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class CustomOptionOption extends TypeOption
{

    private bool $withIdentifikatorField;
    public function __construct(bool$withIdentifikatorField = false) {
        $this->withIdentifikatorField = $withIdentifikatorField;
    }

    public function getDefaultValue(): mixed {
        return [];
    }

    public function getComponent(string $name): Component {
        return Group::make()
            ->columnSpanFull()
            ->schema([
                Group::make()
                    ->schema(fn($get)=> is_null($get("../general_field_id"))? []:[$this->getCustomOptionsSelector($name)])
                    ->hidden(fn($get)=> is_null($get("../general_field_id")))
                    ->columnSpanFull(),
                Group::make()
                    ->schema(fn($get)=> is_null($get("../general_field_id"))? [$this->getCustomOptionsRepeater($name)]:[])
                    ->visible(fn($get)=> is_null($get("../general_field_id")))
                    ->columnSpanFull(),
            ]);
    }



    public function mutateOnCreate(mixed $value, CustomField $field): mixed {
        $field->save();
        return $this->mutateOnSave($value,$field);
    }

    public function mutateOnSave(mixed $value, CustomField $field): mixed {
        //CustomOption::query()->
        if(!$field->isGeneralField()) {
            foreach ($value as $key => $data) if(empty($data["identifier"])) $value[$key]["identifier"] = uniqid();

            return $value;
        }
        if(is_null($value)) return null;
        $field->customOptions()->sync($value);
        return null;
    }


    public function mutateOnLoad(mixed $value, CustomField $field): mixed {
        if(!$field->isGeneralField()) return $value;
        return $field->customOptions->pluck("id")->toArray();
    }



    private function getCustomOptionsSelector ($name):Component {
        return  Select::make($name)
            ->label("Mögliche Auswahlmöglichkeiten")
            ->columnSpanFull()
            ->multiple()
            ->options(function($get){
                $generalField = GeneralField::cached($get("../general_field_id"));
                return $generalField->customOptions->pluck("name_de","id"); //toDo Translate;
            });
    }

    private function getCustomOptionsRepeater($name): Repeater {
        $repeater =Repeater::make($name)
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
                    ->visible($this->withIdentifikatorField)
                    ->columnSpanFull()
                    ->required(),
            ]);
        if($this->withIdentifikatorField) $repeater->relationship("customOptions");
        return $repeater;
    }

}
