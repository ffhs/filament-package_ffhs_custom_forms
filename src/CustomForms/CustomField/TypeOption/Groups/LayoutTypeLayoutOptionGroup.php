<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;

class LayoutTypeLayoutOptionGroup extends TypeOptionGroup
{
    public function __construct(
        string $name = "Layout",
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ) { //ToDo translate
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            "columns" => ColumnsOption::make(),
            'new_line' => NewLineOption::make(),
        ]);
    }

    public static function make(
        string $name = "Layout",
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ): static { //ToDo translate
        return parent::make($name, $typeOptions, $icon);
    }


}
