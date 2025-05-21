<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Component;

class LayoutOptionGroup extends TypeOptionGroup
{
    public function __construct(
        string $name = "Layout",
        array $typeOptions = [],
        ?string $icon = 'bi-layout-text-window'
    ) { //ToDo translate
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            'column_span' => ColumnSpanOption::make(),
            'in_line_label' => InLineLabelOption::make(),
            'new_line' => NewLineOption::make()->modifyOptionComponent(
                fn(Component $component) => $component->columnStart(1)
            ),
            "helper_text" => HelperTextTypeOption::make(),
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
