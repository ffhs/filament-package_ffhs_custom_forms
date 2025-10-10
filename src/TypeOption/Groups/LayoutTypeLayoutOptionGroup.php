<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

class LayoutTypeLayoutOptionGroup extends TypeOptionGroup
{
    public function __construct(?string $name = null, array $typeOptions = [], ?string $icon = 'bi-layout-text-window')
    {
        $name = $name ?? TypeOption::__('layout-group.label');
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            'columns' => ColumnsOption::make(),
            'new_line' => NewLineOption::make(),
        ]);
    }

    public static function make(
        string $name = 'Layout',
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ): static {
        return parent::make($name, $typeOptions, $icon);
    }
}
