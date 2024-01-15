<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Views\SectionTypeView;

class SectionType extends CustomLayoutType
{

    use HasCustomFormPackageTranslation;

    public function viewModes(): array {
        return [
            "default" => SectionTypeView::class
        ];
    }

    public static function getFieldIdentifier(): string {
        return "section";
    }
}
