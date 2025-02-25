<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\ToggleButtonsView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutTypeWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class ToggleButtonsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "toggle_buttons";
    }

    public function viewModes(): array
    {
        return [
            'default' => ToggleButtonsView::class,
        ];
    }


    public function prepareSaveFieldData(CustomFieldAnswer $answer, mixed $data): ?array
    { //ToDo Rename and in Template
        if ($data == '0') $data = false;
        return parent::prepareSaveFieldData($answer, $data);
    }

    public function icon(): string
    {
        return "bi-toggles";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeWithColumnsOptionGroup::make()
                ->addTypeOptions(
                    "inline",
                    InlineOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component->hidden(fn($get) => $get("grouped"))
                        )
                )
                ->addTypeOptions(
                    "grouped",
                    new FastTypeOption(
                        false,
                        Toggle::make("grouped")
                            ->disabled(fn($get) => $get("inline"))
                            ->live()
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.toggle_grouped"
                                )
                            ),
                    )
                )
                ->addTypeOptions(
                    "boolean",
                    BooleanOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component
                            ->disabled(fn($get) => $get("multiple"))
                            ->live(),
                        )
                ),
            ValidationTypeOptionGroup::make(),
            CustomOptionGroup::make()
                ->setTypeOptions([
                    'customOptions' => CustomOptionTypeOption::make()
                        ->modifyOptionComponent(
                            fn(Component $component) => $component->hidden(fn($get) => $get("boolean"))
                        ),
                ]),
        ];
    }

}
