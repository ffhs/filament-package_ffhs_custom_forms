<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionGeneralTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;

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
