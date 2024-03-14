<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ToggleButtonsType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
        HasBasicSettings::getExtraOptionSchema as getExtraOptionSchemaBasicSetup;
    }

    public static function getFieldIdentifier(): string { return "toggle_buttons"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\ToggleButtonsView::class,
        ];
    }
    public function getExtraOptionSchema() : array{

        return [
            Group::make()
                ->statePath("options")
                ->columnSpanFull()
                ->columns()
                ->schema([
                    Group::make()
                        ->columnSpanFull()
                        ->columns()
                        ->schema(array_merge($this->getExtraOptionSchemaBasicSetup(),[
                            Toggle::make("inline")
                                ->disabled(fn($get)=> $get("grouped"))
                                ->label("In der Zeile"),//ToDo Translate
                            Toggle::make("grouped")
                                ->disabled(fn($get)=> $get("inline"))
                                ->label("Gruppeiert")//ToDo Translate
                                ->columnStart(2),

                            Toggle::make("boolean")
                                ->disabled(fn($get)=> $get("multiple"))
                                ->label("Ja/Nein")//ToDo Translate
                                ->columnStart(1),
                            Toggle::make("multiple")
                                ->columnStart(2)
                                ->disabled(fn($get)=> $get("boolean"))
                                ->label("Mehre auswählbar"),//ToDo Translate

                            TextInput::make("columns")
                                ->columnStart(1)
                                ->disabled(fn($get)=> $get("grouped") ||  $get("inline")||  $get("boolean"))
                                ->label("Spalten")//ToDo Translate
                                ->numeric()
                        ])),
                    Group::make()
                        ->hidden(fn($get)=> is_null($get("../../../general_field_id")) || $get("boolean"))
                        ->columnSpanFull()
                        ->schema(function ($get){
                            if(!is_null($get("../../../general_field_id"))) return [$this->getCustomOptionsSelector()];
                            return [];
                        }),
                ]),
            Group::make()
                ->columnSpanFull()
                ->schema(function ($get){
                    if(is_null($get("../../general_field_id")) || $get("options.boolean")) return [$this->getCustomOptionsRepeater()];
                    return [];
                }),
        ];
    }


    public function getExtraOptionFieldsBasicOptions():array{
        return [
            'inline' => false,
            'grouped'=>false,
            'boolean'=> false,
            'multiple'=> false,
            'columns'=> 2,
        ];
    }
    public function icon(): String {
        return  "bi-toggles";
    }
}
