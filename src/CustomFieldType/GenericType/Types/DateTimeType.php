<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\DateTimeTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\DateFormatOption;

class DateTimeType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'date-time';
    protected static string $icon = 'heroicon-s-clock';
    protected static array $viewModes = [
        'default' => DateTimeTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->addTypeOptions(
                    'format',
                    DateFormatOption::make()
                        ->modifyDefault('Y-m-d H:i:s')
                ),
        ];
    }
}
