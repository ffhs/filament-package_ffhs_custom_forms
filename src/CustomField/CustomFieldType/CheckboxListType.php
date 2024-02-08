<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;

class CheckboxListType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
        HasBasicSettings::getExtraOptionSchema as getExtraOptionSchemaBasicSetup;
    }

    public static function getFieldIdentifier(): string { return "checkbox_list"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\CheckboxListTypeView::class,
        ];
    }

    public function getExtraOptionSchemaHasOptions() : array{
        return  $this->getExtraOptionSchemaBasicSetup();
    }

    public function icon(): String {
        return  "bi-ui-checks-grid";
    }
}
