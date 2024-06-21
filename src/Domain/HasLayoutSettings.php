<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Domain;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;

trait HasLayoutSettings
{
    public function getExtraTypeOptions(): array{
        return [
            TypeOptionGroup::make("Layout", [ //toDo Tranlsate
                'column_span' => ColumnSpanOption::make(),
                'new_line_option' => NewLineOption::make(),
            ])
        ];
    }

}
