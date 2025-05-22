<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\ToggleButtonsView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
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
        if ($data == '0') {
            $data = false;
        }
        return parent::prepareSaveFieldData($answer, $data);
    }

    public function icon(): string
    {
        return "bi-toggles";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make()
                ->mergeTypeOptions([
                    'inline' => InlineOption::make()
                        ->modifyOptionComponent(function (Toggle $component) {
                            return $component->hidden(fn($get) => $get("grouped"));
                        }),
                    'grouped' => FastTypeOption::makeFast(
                        false,
                        Toggle::make("grouped")
                            ->helperText(TypeOption::__('toggle_grouped.helper_text'))
                            ->label(TypeOption::__('toggle_grouped.label'))
                            ->disabled(fn($get) => $get("inline"))
                            ->live(),
                    ),
                    'boolean' => BooleanOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component
                            ->disabled(fn($get) => $get("multiple"))
                            ->live(),
                        ),
                ]),
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
