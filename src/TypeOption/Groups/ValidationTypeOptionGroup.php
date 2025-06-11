<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ValidationMessageOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

class ValidationTypeOptionGroup extends TypeOptionGroup
{
    public static function make(
        string $name = 'Validation',
        array $typeOptions = [],
        ?string $icon = 'carbon-scan-alt'
    ): static {
        if (empty($typeOptions)) {
            $typeOptions = [
                'validation_attribute' => ValidationAttributeOption::make(),
                'validation_messages' => ValidationMessageOption::make(),
                'required' => RequiredOption::make(),
            ];
        }
        return parent::make(TypeOption::__('validation-group.label'), $typeOptions, $icon);
    }
}
