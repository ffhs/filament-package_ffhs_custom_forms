<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

class CustomOptionGroup extends TypeOptionGroup
{
    public function __construct(
        ?string $name = null,
        array $typeOptions = [],
        ?string $icon = 'heroicon-m-queue-list'
    ) {
        $name = $name ?? CustomOption::__('label.multiple');
        parent::__construct($name, $typeOptions, $icon);

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
