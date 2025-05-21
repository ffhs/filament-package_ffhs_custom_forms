<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGeneralTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;

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
