<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\ColorPickerTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColorTypeOption;

class ColorPickerType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'color_input';
    protected static string $icon = 'carbon-color-palette';
    protected static array $viewModes = [
        'default' => ColorPickerTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->addTypeOptions('color_type', ColorTypeOption::make()),
        ];
    }
}
