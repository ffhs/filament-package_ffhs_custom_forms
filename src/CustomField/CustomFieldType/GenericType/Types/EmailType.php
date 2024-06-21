<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\EmailTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;

class EmailType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    use HasBasicSettings;

    public static function identifier(): string {return "email";}


    public function viewModes(): array {
        return [
            "default"=> EmailTypeView::class,
        ];
    }

    public function icon(): string {
        return  "carbon-email";
    }
}
