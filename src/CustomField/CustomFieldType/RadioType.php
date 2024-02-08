<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasTypeOptions;

class RadioType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings,HasTypeOptions{
        HasTypeOptions::getExtraOptionSchema insteadof HasBasicSettings;
        HasBasicSettings::getExtraOptionSchema as getExtraOptionSchemaBasicSetup;
    }

    public static function getFieldIdentifier(): string { return "radio"; }

    public function viewModes(): array {
        return  [
            'default'  => CustomFieldType\Views\RadioTypeView::class,
        ];
    }
    public function getExtraOptionSchemaHasOptions() : array{
        return  $this->getExtraOptionSchemaBasicSetup();
    }

    public function icon(): String {
        return  "carbon-radio-button-checked";
    }
}
