<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HiddenLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Toggle;

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
            'helper_text' => HelperTextTypeOption::make(),
            'hidden_label' => HiddenLabelOption::make()
                ->modifyOptionComponent(function (Toggle $component) {
                    return $component
                        ->afterStateUpdated(fn($set) => $set('in_line_label', false))
                        ->live();
                }),
            'in_line_label' => InLineLabelOption::make()
                ->modifyOptionComponent(function (Toggle $component) {
                    return $component->disabled(fn($get) => $get('hidden_label'));
                }),
            'new_line' => NewLineOption::make()
                ->modifyOptionComponent(fn(Toggle $component) => $component->columnStart(1)),
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
