<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\RadioTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Component;

class RadioType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string { return "radio"; }

    public function viewModes(): array {
        return  [
            'default'  => RadioTypeView::class,
        ];
    }

    public function extraTypeOptions(): array {

        return [
            DefaultLayoutTypeOptionGroup::make()
                ->setTypeOptions([
                    'column_span' => ColumnSpanOption::make(),
                    "columns" => new ColumnsOption(),
                    'new_line_option' => NewLineOption::make()->modifyComponent(fn(Component $component) => $component->columnStart(1)),
                ]),
            CustomOptionGroup::make()
        ];
    }


    public function icon(): String {
        return  "carbon-radio-button-checked";
    }
}
