<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;

class LayoutTypeDefaultLayoutTypeOptionGroup extends TypeOptionGroup
{
    public static function make(string $name= "Layout", array $typeOptions = [], ?string $icon = 'bi-layout-text-window'): static { //ToDo translate
        return parent::make($name, $typeOptions, $icon);
    }


    public function __construct(string $name= "Layout", array $typeOptions = [], ?string $icon = 'bi-layout-text-window') { //ToDo translate
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            "columns" => ColumnsOption::make(),
            'new_line_option' => NewLineOption::make(),
        ]);
    }


}
