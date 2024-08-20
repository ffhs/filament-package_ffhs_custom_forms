<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\SelectTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SelectType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string { return "select"; }

    public function viewModes(): array {
        return  [
            'default' => SelectTypeView::class,
        ];
    }
    public function icon(): String {
        return  "carbon-select-window";
    }

    public function generalTypeOptions(): array
    {
        return [
            TypeOptionGroup::make("Optionen",   [
                "customOptions" => new CustomOptionTypeOption()
            ],"heroicon-m-queue-list")
        ];
    }


    public function extraTypeOptions(): array {
        return
            [
                DefaultLayoutTypeOptionGroup::make(),
                ValidationTypeOptionGroup::make()
                    ->setTypeOptions([
                        "several" => new FastTypeOption(false,
                            Toggle::make("several")
                                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.several"))
                                ->columnSpanFull()
                                ->live()
                        ),
                        "min_select" => new FastTypeOption(1,
                            TextInput::make("min_select")
                                ->hidden(fn($get)=> !$get("several"))
                                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select"))
                                ->columnStart(1)
                                ->helperText(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select_helper"))
                                ->minValue(0)
                                ->step(1)
                                ->required()
                                ->numeric(),
                        ),
                        "max_select" => new FastTypeOption(1,
                            TextInput::make("max_select")
                                ->hidden(fn($get)=> !$get("several"))
                                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select"))
                                ->helperText(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select_helper"))
                                ->minValue(0)
                                ->step(1)
                                ->required()
                                ->numeric(),
                        ),
                    ]),
                TypeOptionGroup::make("Optionen",   [
                    "customOptions" => new CustomOptionTypeOption()
                ],"heroicon-m-queue-list"),
            ];
    }


}
