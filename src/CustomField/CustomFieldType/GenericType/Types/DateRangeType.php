<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\DateRangeTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;

class DateRangeType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;


    public static function identifier(): string {return "date_range";}

    public function viewModes(): array {
        return  [
            'default'  => DateRangeTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-calendar-range";
    }


    public function extraTypeOptions(): array {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidationTypeOptionGroup::make(typeOptions: [
                'required' => RequiredOption::make()
            ] )
        ];
    }


}
