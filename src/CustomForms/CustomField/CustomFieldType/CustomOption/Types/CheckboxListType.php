<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\TextInput;

class CheckboxListType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "checkbox_list";
    }

    public function viewModes(): array
    {
        return [
            'default' => CheckboxListTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-ui-checks-grid";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    "min_items" => new FastTypeOption( //ToDO Make CUstom Options? WIth repeater?
                        null,
                        TextInput::make("min_items")
                            ->hidden(fn($get) => !$get("several"))
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select")
                            )
                            ->columnStart(1)
                            ->helperText(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select_helper"
                                )
                            )
                            ->minValue(0)
                            ->step(1)
                            ->numeric(),
                    ),
                    "max_items" => new FastTypeOption(
                        null,
                        TextInput::make("max_items")
                            ->hidden(fn($get) => !$get("several"))
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select")
                            )
                            ->helperText(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select_helper"
                                )
                            )
                            ->minValue(0)
                            ->step(1)
                            ->numeric(),
                    ),
                ]),
            CustomOptionGroup::make(),
        ];
    }


}
