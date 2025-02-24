<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\RadioTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Filament\Forms\Components\Component;

class RadioType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "radio";
    }

    public function viewModes(): array
    {
        return [
            'default' => RadioTypeView::class,
        ];
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
                ]),
            CustomOptionGroup::make(),
            ValidationTypeOptionGroup::make(),
        ];
    }


    public function icon(): string
    {
        return "carbon-radio-button-checked";
    }
}
