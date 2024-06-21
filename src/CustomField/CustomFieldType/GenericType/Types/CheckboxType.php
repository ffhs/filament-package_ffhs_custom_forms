<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\CheckboxTypeView;

class CheckboxType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string { return "checkbox"; }

    public function viewModes(): array {
        return  [
            'default'  => CheckboxTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-check-square";
    }
}
