<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\DateTimeTypeView;

class CheckboxType extends CustomFieldType
{
    public static function getFieldIdentifier(): string { return "checkbox"; }

    public function viewModes(): array {
        return  [
            'default'  => DateTimeTypeView::class
        ];
    }
}
