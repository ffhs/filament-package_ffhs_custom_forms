<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HiddenLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;

class LayoutOptionGroup extends TypeOptionGroup
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
            'in_line_label' => InLineLabelOption::make(),
            'new_line' => NewLineOption::make()->modifyDefault(fn($default) => 1),
            'helper_text' => HelperTextTypeOption::make(),
            'hidden_label' => HiddenLabelOption::make()
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
