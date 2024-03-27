<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\EmailTypeView;

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

    public function icon(): string {
        return  "carbon-email";
    }
}
