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
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
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

//    public function prepareLoadFieldData(CustomFieldAnswer $answer, array $data): mixed { //ToDo Rename and in Template
//        $data = parent::prepareLoadFieldData($answer, $data);
//        if($data == false) dd($data);
//        return $data;
//    }


    public function icon(): string
    {
        return "bi-toggles";
    }

    public function extraTypeOptions(): array
    {
        return [
            DefaultLayoutTypeOptionGroup::make()
                ->setTypeOptions([
                    'column_span' => ColumnSpanOption::make(),
                    "columns" => ColumnsOption::make(),
                    'new_line_option' => NewLineOption::make()->modifyOptionComponent(
                        fn(Component $component) => $component->columnStart(1)
                    ),

                    "inline" => InlineOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component->hidden(fn($get) => $get("grouped"))
                        ),

                    "grouped" => new FastTypeOption(
                        false,
                        Toggle::make("grouped")
                            ->disabled(fn($get) => $get("inline"))
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.toggle_grouped"
                                )
                            )
                            ->live(),
                    ),
                    "boolean" => BooleanOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component
                            ->disabled(fn($get) => $get("multiple"))
                            ->live(),
                        ),
                ]),
            CustomOptionGroup::make()
                ->setTypeOptions([
                    'required' => RequiredOption::make(),
                    'customOptions' => parent::extraTypeOptions()["customOptions"]
                        ->modifyOptionComponent(
                            fn(Component $component) => $component->hidden(fn($get) => $get("boolean"))
                        ),
                ]),
        ];
    }

}
