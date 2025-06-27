<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;

class LayoutWithColumnsOptionGroup extends LayoutOptionGroup
{
    public function __construct(
        string $name = 'Layout',
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ) {
        parent::__construct(TypeOption::__('layout-group.label'), $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            'columns' => ColumnsOption::make(),
            'in_line_label' => InLineLabelOption::make(),
            'new_line' => NewLineOption::make()->modifyOptionComponent(
                fn(Component $component) => $component->columnStart(1)
            ),
            'helper_text' => HelperTextTypeOption::make(),
        ]);
    }
}
