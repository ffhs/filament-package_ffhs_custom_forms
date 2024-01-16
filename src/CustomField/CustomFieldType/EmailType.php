<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\EmailTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;

class EmailType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "email";}


    public function viewModes(): array {
        return [
            "default"=> EmailTypeView::class,
        ];
    }
}
