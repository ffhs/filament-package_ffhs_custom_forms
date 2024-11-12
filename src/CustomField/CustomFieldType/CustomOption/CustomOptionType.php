<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;

abstract class  CustomOptionType extends CustomFieldType
{

    public function extraTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionTypeOption(),
        ];
    }

    public function generalTypeOptions(): array {
        return [
            "customOptions" => new CustomOptionTypeOption(),
        ];
    }


}
