<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views\CheckboxListTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxSelectOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinSelectOption;

class CheckboxListType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'checkbox_list';
    protected static string $icon = 'bi-ui-checks-grid';
    protected static array $viewModes = [
        'default' => CheckboxListTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'min_items' => MinSelectOption::make(),
                    'max_items' => MaxSelectOption::make(),
                ]),
            CustomOptionGroup::make(),
        ];
    }
}
