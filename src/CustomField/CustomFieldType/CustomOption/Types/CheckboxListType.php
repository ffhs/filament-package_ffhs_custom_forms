<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CheckboxListType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => CheckboxListTypeView::class,
        ];
    }
    public function icon(): String {
        return  "bi-ui-checks-grid";
    }

    public function extraTypeOptions(): array {
        return [
          DefaultLayoutTypeOptionGroup::make()
            ->setTypeOptions([
                'column_span' => ColumnSpanOption::make(),
                "columns" => new ColumnsOption(),
                'new_line_option' => NewLineOption::make()->modifyComponent(fn(Component $component) => $component->columnStart(1)),
            ]),
            ValidationTypeOptionGroup::make()
                ->setTypeOptions([
                    "min_items" => new FastTypeOption(null,
                        TextInput::make("min_select")
                            ->hidden(fn($get)=> !$get("several"))
                            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select"))
                            ->columnStart(1)
                            ->helperText(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select_helper"))
                            ->minValue(0)
                            ->step(1)
                            ->numeric(),
                    ),
                    "max_items" => new FastTypeOption(null,
                        TextInput::make("max_select")
                            ->hidden(fn($get)=> !$get("several"))
                            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select"))
                            ->helperText(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select_helper"))
                            ->minValue(0)
                            ->step(1)
                            ->numeric(),
                    ),
                ]),
          CustomOptionGroup::make()


        ];
    }



}
