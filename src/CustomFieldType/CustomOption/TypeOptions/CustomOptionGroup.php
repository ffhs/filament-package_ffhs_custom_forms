<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;

class CustomOptionGroup extends TypeOptionGroup
{
    public function __construct(
        string $name = 'Optionen',
        array $typeOptions = [],
        ?string $icon = 'heroicon-m-queue-list'
    ) {
        parent::__construct(CustomOption::__('label.multiple'), $typeOptions, $icon);

        $this->mergeTypeOptions([
            'customOptions' => CustomOptionTypeOption::make(),
        ]);
    }

    public static function make(
        string $name = 'Optionen',
        array $typeOptions = [],
        ?string $icon = 'heroicon-m-queue-list'
    ): static {
        return parent::make($name, $typeOptions, $icon);
    }
}
