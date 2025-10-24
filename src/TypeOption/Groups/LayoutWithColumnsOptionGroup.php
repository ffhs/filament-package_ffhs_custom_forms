<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;

class LayoutWithColumnsOptionGroup extends LayoutOptionGroup
{
    public function __construct(
        ?string $name = null,
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ) {
        $name = $name ?? TypeOption::__('layout-group.label');
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            'columns' => ColumnsOption::make(),
            'in_line_label' => InLineLabelOption::make(),
            'new_line' => NewLineOption::make(),
            'helper_text' => HelperTextTypeOption::make(),
        ]);
    }
}
