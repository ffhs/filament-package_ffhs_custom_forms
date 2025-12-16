<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\CheckboxTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;

class CheckboxType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'checkbox';
    protected static string $icon = 'bi-check-square';
    protected static array $viewModes = [
        'default' => CheckboxTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->removeTypeOption('column_span'),
            ValidationTypeOptionGroup::make(),
        ];
    }
}
