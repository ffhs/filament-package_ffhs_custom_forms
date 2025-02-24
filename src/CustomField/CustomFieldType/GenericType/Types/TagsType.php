<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TagsTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Filament\Forms\Components\Component;

class TagsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "tags_input";
    }

    public function viewModes(): array
    {
        return [
            'default' => TagsTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-tags";
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

        ];
    }


}
