<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

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
    public function getExtraTypeOptions(): array{
        return array_merge(
            $this->extraOptionsBeforeBasic(),
            [
                'column_span' => new ColumnSpanOption(),
                'in_line_label' => new InLineLabelOption(),
                'new_line_option' => new NewLineOption(),
            ],
            $this->extraOptionsAfterBasic()
        );
    }

}
