<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\NumberTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinValueOption;

class NumberType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'number';
    protected static string $icon = 'carbon-character-whole-number';
    protected static array $viewModes = [
        'default' => NumberTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'min_value' => new MinValueOption(),
                    'max_value' => new MaxValueOption(),
                ]),
        ];
    }
}
