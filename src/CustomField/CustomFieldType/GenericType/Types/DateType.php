<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\DateTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\CustomValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\DateFormatOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;

class DateType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;


    public static function identifier(): string
    {
        return "date";
    }

    public function viewModes(): array
    {
        return [
            'default' => DateTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-calendar3";
    }


    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make(typeOptions: [
                'required' => RequiredOption::make(),
                'validation_attribute' => CustomValidationAttributeOption::make(),
                'format' => DateFormatOption::make(),
            ]),
        ];
    }


}
