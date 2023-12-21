<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\EmailTypeView;

class EmailType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function getFieldIdentifier(): string {return "email";}

    public function viewModes(): array {
        return  [
          'default'  => EmailTypeView::class
        ];
    }
}
