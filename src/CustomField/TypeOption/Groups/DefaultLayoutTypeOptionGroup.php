<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\HelptextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Component;

class DefaultLayoutTypeOptionGroup extends TypeOptionGroup
{
    public static function make(string $name= "Layout", array $typeOptions = [], ?string $icon = 'bi-layout-text-window'): static { //ToDo translate
        return parent::make($name, $typeOptions, $icon);
    }


    public function __construct(string $name= "Layout", array $typeOptions = [], ?string $icon = 'bi-layout-text-window') { //ToDo translate
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            //ToDo add Help Text
            'column_span' => ColumnSpanOption::make(),
            'new_line_option' => NewLineOption::make()->modifyComponent(fn(Component $component) => $component->columnStart(1)),
            "helper_text" => HelptextTypeOption::make()
        ]);
    }


}
