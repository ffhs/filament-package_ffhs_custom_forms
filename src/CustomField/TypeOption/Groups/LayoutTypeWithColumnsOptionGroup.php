<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Filament\Forms\Components\Component;

class LayoutTypeWithColumnsOptionGroup extends DefaultLayoutTypeOptionGroup
{
    public function __construct(
        string $name = "Layout",
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ) {
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            "columns" => ColumnsOption::make(),
            'new_line_option' => NewLineOption::make()->modifyOptionComponent(
                fn(Component $component) => $component->columnStart(1)
            ),
            "helper_text" => HelperTextTypeOption::make(),
        ]);
    }


}
