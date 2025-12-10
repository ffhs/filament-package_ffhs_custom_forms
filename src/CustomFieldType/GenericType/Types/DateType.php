<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\DateFormatOption;

class DateType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'date';
    protected static string $icon = 'bi-calendar3';
    protected static array $viewModes = [
        'default' => DateTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->addTypeOptions('format', DateFormatOption::make()),
        ];
    }
}
