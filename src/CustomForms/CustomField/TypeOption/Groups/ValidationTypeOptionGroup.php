<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ValidationMessageOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;

class ValidationTypeOptionGroup extends TypeOptionGroup
{
    public static function make(
        string $name = "Validation",
        array $typeOptions = [],
        ?string $icon = 'carbon-scan-alt'
    ): static { //ToDo translate
        if (empty($typeOptions)) {
            $typeOptions = [
                'validation_attribute' => ValidationAttributeOption::make(),
                'validation_messages' => ValidationMessageOption::make(),
                'required' => RequiredOption::make(),
            ];
        }
        return parent::make($name, $typeOptions, $icon);
    }
}
