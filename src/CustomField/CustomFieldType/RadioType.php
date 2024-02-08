<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RadioType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
        HasBasicSettings::getExtraOptionSchema as getExtraOptionSchemaBasicSetup;
    }

    public static function getFieldIdentifier(): string { return "radio"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\RadioTypeView::class,
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
                            Toggle::make("boolean")
                                ->label("Ja/Nein"),//ToDo Translate
                            Toggle::make("inline")
                                ->label("In der Zeile"),//ToDo Translate
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
            'boolean'=> false,
        ];
    }

    public function icon(): String {
        return  "carbon-radio-button-checked";
    }
}
