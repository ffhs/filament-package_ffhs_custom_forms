<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\NumberTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;

class NumberType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string {return "number";}

    public function viewModes(): array {
        return [
            'default' => NumberTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-number";
    }

    public function extraTypeOptions(): array {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidationTypeOptionGroup::make(typeOptions: [
                'min_value'=>new MinValueOption(),
                'max_value'=>new MaxValueOption(),
                'required' => RequiredOption::make(),
            ] )
        ];
    }


}
