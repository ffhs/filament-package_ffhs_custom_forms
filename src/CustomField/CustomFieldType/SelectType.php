<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SelectType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    use HasTypeOptions;

    public static function getFieldIdentifier(): string { return "select"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\SelectTypeView::class, //ToDo
        ];
    }



    public function getExtraOptionSchema():?array{
        return [
            Group::make()
                ->statePath("options")
                ->columnSpanFull()
                ->columns()
                ->schema([
                    $this->getNewLineOption()->columnStart(1),
                    $this->getInLineLabelOption(),
                    $this->getColumnSpanOption(),

                    Toggle::make("several")
                        ->label("Mehre auswählbar")//ToDo Translate
                        ->columnSpanFull()
                        ->live(),

                    TextInput::make("min_select")
                        ->hidden(fn($get)=> !$get("several"))
                        ->label("Mindestanzahl") //ToDo Translate
                        ->columnStart(1)
                        ->helperText("Greift nur bei (Benötigt)")//ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),

                    TextInput::make("max_select")
                        ->hidden(fn($get)=> !$get("several"))
                        ->label("Maximalanzahl") //ToDo Translate
                        ->helperText("'0' entspricht keine Begrenzung") //ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),

                    Group::make()
                        ->columnSpanFull()
                        ->schema(function ($get){
                            if(!is_null($get("../../../../general_field_id"))) return [$this->getCustomOptionsSelector()];
                            return [];
                        }),
                ]),

            Group::make()
                ->columnSpanFull()
                ->schema(function ($get){
                    if(is_null($get("../../../general_field_id"))) return [$this->getCustomOptionsRepeater()];
                    return [];
                }),
        ];
    }

    public function getExtraOptionFields():array{ //ToDo dont work
        return [
            "customOptions" => [],
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,

            'several' => false,
            'min_select'=>1,
            'max_select'=> 0,
        ];
    }


    public function icon(): String {
        return  "carbon-select-window";
    }
}
