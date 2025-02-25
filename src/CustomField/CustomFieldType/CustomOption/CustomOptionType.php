<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGeneralTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;

abstract class CustomOptionType extends CustomFieldType
{
    public function extraTypeOptions(): array
    {
        return [
            CustomOptionGroup::make(),
        ];
    }

    public function generalTypeOptions(): array
    {
        return [
            CustomOptionGroup::make()
                ->setTypeOptions([
                    'customOptions' => CustomOptionGeneralTypeOption::make(),
                ]),
        ];
    }
}
