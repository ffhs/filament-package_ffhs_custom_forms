<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
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
            'default'  => CustomFieldType\Views\SelecTypeView::class, //ToDo
        ];
    }

    public function getExtraOptionSchema(?GeneralField $generalField = null):?array{
        $schema = [
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
                ->helperText("Greift nur bei (Benötigt)")//ToDo Translate
                ->columnStart(1)
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


        ];

        if(is_null($generalField))$schema[] = $this->getCustomOptionsRepeater(true);
        if(!is_null($generalField))$schema[] = $this->getCustomOptionsSelector($generalField);

        return $schema;
    }

    public function getExtraOptionFields(?GeneralField $generalField = null):array{
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
