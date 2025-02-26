<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\CustomValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ValidationMessageOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;

class ValidationTypeOptionGroup extends TypeOptionGroup
{
    public static function make(
        string $name = "Validation",
        array $typeOptions = [],
        ?string $icon = 'carbon-scan-alt'
    ): static { //ToDo translate
        if (empty($typeOptions)) {
            $typeOptions = [
                'validation_attribute' => CustomValidationAttributeOption::make(),
                'validation_messages' => ValidationMessageOption::make(),
                'required' => RequiredOption::make(),
            ];
        }
        return parent::make($name, $typeOptions, $icon);
    }
}
