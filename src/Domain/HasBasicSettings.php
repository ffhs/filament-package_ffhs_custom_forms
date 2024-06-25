<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Domain;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;

trait HasBasicSettings
{
    protected function extraOptionsBeforeBasic(): array{
        return [];
    }
    protected function extraOptionsAfterBasic(): array{
        return [];
    }
    public function extraTypeOptions(): array{
        return array_merge(
            $this->extraOptionsBeforeBasic(),
            [
                'column_span' => ColumnSpanOption::make(),
                'in_line_label' => InLineLabelOption::make()->modifyComponent(fn($toggle) => $toggle->columnStart(1)),
                'new_line_option' => NewLineOption::make(),
            ],
            $this->extraOptionsAfterBasic()
        );
    }

}
