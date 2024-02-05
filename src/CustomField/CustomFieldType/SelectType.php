<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\CheckboxTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use PHPUnit\Metadata\Group;

class SelectType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    use HasTypeOptions;

    public static function getFieldIdentifier(): string { return "select"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\SelecTypeView::class, //ToDo
        ];
    }

    public function getExtraOptionSchema():?array{
        return [
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption(),
            $this->getColumnSpanOption(),

            Toggle::make("several")
                ->label("Mehre auswählbar")//ToDo Translate
                ->columnSpanFull()
                ->live(), //WHY THAT DONT WORK

            \Filament\Forms\Components\Group::make()
                ->hidden(fn($get)=> Debugbar::info(!$get("several"))) //WHY THAT DONT WORK
                ->columnSpanFull()
                ->columns()
                ->hidden()
                ->schema([
                    TextInput::make("min_select")
                        ->label("Mindestanzahl") //ToDo Translate
                        ->helperText("Greift nur bei (Benötigt)")//ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),
                    TextInput::make("max_select")
                        ->label("Maximalanzahl") //ToDo Translate
                        ->helperText("'0' entspricht keine Begrenzung") //ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),
                ]),


            $this->getCustomOptionsRepeater(),
        ];
    }

    public function getExtraOptionFields():array{
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
