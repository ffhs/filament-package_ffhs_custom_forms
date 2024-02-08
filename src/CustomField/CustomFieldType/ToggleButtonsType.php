<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;
use Filament\Forms\Components\Builder;
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
            'default'  => CustomFieldType\Views\ToggleButtonsView::class,
        ];
    }
    public function getExtraOptionSchemaHasOptions() : array{
        return array_merge($this->getExtraOptionSchemaBasicSetup(), [
            Toggle::make("inline")
                ->label("Mehre auswählbar"),//ToDo Translate
            Toggle::make("grouped")
                ->label("Gruppeiert"),//ToDo Translate
            Toggle::make("boolean")
                ->label("Ja/Nein"),//ToDo Translate
            Toggle::make("multiple")
                ->label("Mehre auswählbar"),//ToDo Translate
            TextInput::make("columns")
                ->label("Spalten")//ToDo Translate
                ->numeric()


        ]);
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
