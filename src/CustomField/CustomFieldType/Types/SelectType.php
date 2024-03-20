<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SelectType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function getFieldIdentifier(): string { return "select"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\SelectTypeView::class,
        ];
    }
    public function icon(): String {
        return  "carbon-select-window";
    }

    public function getExtraTypeOptions(): array {
        return array_merge(
            $this->getExtraSettingsOptions(),
            [
                "several" => new FastTypeOption(false,
                    Toggle::make("several")
                        ->label("Mehre auswählbar")//ToDo Translate
                        ->columnSpanFull()
                        ->live()
                ),
                "min_select" => new FastTypeOption(1,
                    TextInput::make("min_select")
                        ->hidden(fn($get)=> !$get("several"))
                        ->label("Mindestanzahl") //ToDo Translate
                        ->columnStart(1)
                        ->helperText("Greift nur bei (Benötigt)")//ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),
                ),
                "max_select" => new FastTypeOption(1,
                    TextInput::make("max_select")
                        ->hidden(fn($get)=> !$get("several"))
                        ->label("Maximalanzahl") //ToDo Translate
                        ->helperText("'0' entspricht keine Begrenzung") //ToDo Translate
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->numeric(),
                )
            ],
            parent::getExtraTypeOptions()
        );
    }


}
