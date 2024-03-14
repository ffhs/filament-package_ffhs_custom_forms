<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\CheckboxTypeView;

class CheckboxType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string { return "checkbox"; }

    public function viewModes(): array {
        return  [
            'default'  => CheckboxTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-check-square";
    }
}
