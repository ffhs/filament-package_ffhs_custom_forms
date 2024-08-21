<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\ToggleButtonsView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ToggleButtonsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string { return "toggle_buttons"; }

    public function viewModes(): array {
        return  [
            'default'  => ToggleButtonsView::class,
        ];
    }

    public function icon(): String {
        return  "bi-toggles";
    }
    public function extraTypeOptions(): array {
        return [
            DefaultLayoutTypeOptionGroup::make()
                ->setTypeOptions([
                    'column_span' => ColumnSpanOption::make(),
                    "columns" => new ColumnsOption(),
                    'new_line_option' => NewLineOption::make()->modifyComponent(fn(Component $component) => $component->columnStart(1)),

                    "inline" => (new InlineOption())
                        ->modifyComponent(fn(Toggle $component)=> $component->hidden(fn($get) => $get("grouped"))),

                    "grouped" => new FastTypeOption(false,
                        Toggle::make("grouped")
                            ->disabled(fn($get)=> $get("inline"))
                            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.toggle_grouped"))
                            ->live(),
                    ),
                    "boolean" => (new BooleanOption())
                        ->modifyComponent(fn(Toggle $component)=>
                            $component
                                ->disabled(fn($get)=> $get("multiple"))
                                ->live(),
                    ),
                ]),
            CustomOptionGroup::make()
                ->setTypeOptions([
                    'customOptions' => parent::extraTypeOptions()["customOptions"]
                        ->modifyComponent(fn(Component $component) => $component->hidden(fn($get) => $get("boolean"))),
                ])
        ];
    }

}
