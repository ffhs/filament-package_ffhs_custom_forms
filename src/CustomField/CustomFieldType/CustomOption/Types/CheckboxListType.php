<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutTypeWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\CustomValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
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
            LayoutTypeWithColumnsOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->setTypeOptions([
                    "min_items" => new FastTypeOption(
                        null,
                        TextInput::make("min_select")
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
                        TextInput::make("max_select")
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

                    'validation_attribute' => CustomValidationAttributeOption::make(),
                    'required' => RequiredOption::make(),
                ]),
            CustomOptionGroup::make(),


        ];
    }


}
