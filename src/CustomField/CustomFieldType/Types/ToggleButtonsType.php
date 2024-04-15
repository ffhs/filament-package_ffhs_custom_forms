<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ToggleButtonsType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function getFieldIdentifier(): string { return "toggle_buttons"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\ToggleButtonsView::class,
        ];
    }

    public function icon(): String {
        return  "bi-toggles";
    }
    public function getExtraTypeOptions(): array {
        return array_merge(
            [
                "columns" => (new ColumnsOption())
                    ->modifyComponent(fn($component) => $component
                        ->disabled(fn($get)=> $get("grouped") ||  $get("inline")||  $get("boolean"))
                        ->label("Spalten")//ToDo Translate
                    )
            ],
            $this->getExtraSettingsOptions(),
            [
                "inline" => (new InlineOption())
                    ->modifyComponent(fn(Toggle $component)=> $component->disabled(fn($get)=> $get("grouped"))),
                "grouped" => new FastTypeOption(false,
                    Toggle::make("grouped")
                        ->disabled(fn($get)=> $get("inline"))
                        ->label("Schalter gruppiert")//ToDo Translate
                        ->columnStart(2)
                        ->live(),
                ),
                "boolean" => (new BooleanOption())
                    ->modifyComponent(fn(Toggle $component)=>
                        $component
                            ->disabled(fn($get)=> $get("multiple"))
                            ->columnStart(1)
                ),
              /*  "multiple" => new FastTypeOption(false,
                    Toggle::make("multiple")
                        ->disabled(fn($get)=> $get("boolean"))
                        ->columnStart(2)
                        ->label("Mehre Schalter auswählbar")//ToDo Translate
                        ->live(),
                ),*/
                parent::getExtraTypeOptions()["customOptions" ]
                    ->modifyComponent(fn(Component $component) => $component->hidden(fn($get) => $get("boolean")))
            ],

        );
    }

}
