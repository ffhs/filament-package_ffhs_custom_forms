<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\ToggleButtonsView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ToggleButtonsType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings {
        HasBasicSettings::getExtraTypeOptions as getExtraSettingsOptions;
    }

    public static function identifier(): string { return "toggle_buttons"; }

    public function viewModes(): array {
        return  [
            'default'  => ToggleButtonsView::class,
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
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.columns"))
                    )
            ],
            $this->getExtraSettingsOptions(),
            [
                "inline" => (new InlineOption())
                    ->modifyComponent(fn(Toggle $component)=> $component->disabled(fn($get)=> $get("grouped"))),
                "grouped" => new FastTypeOption(false,
                    Toggle::make("grouped")
                        ->disabled(fn($get)=> $get("inline"))
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.toggle_grouped"))
                        ->columnStart(2)
                        ->live(),
                ),
                "boolean" => (new BooleanOption())
                    ->modifyComponent(fn(Toggle $component)=>
                        $component
                            ->disabled(fn($get)=> $get("multiple"))
                            ->columnStart(1)
                ),
                'customOptions' => parent::getExtraTypeOptions()["customOptions"]
                    ->modifyComponent(fn(Component $component) => $component->hidden(fn($get) => $get("boolean")))
            ],

        );
    }

}
