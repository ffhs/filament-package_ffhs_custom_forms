<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Domain;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;

trait HasFrontLayoutSettings
{
    public function getFrontTypeOptions(): array{
        return [
            'column_span' => ColumnSpanOption::make(),
            'new_line_option' => NewLineOption::make(),
        ];
    }

}
