<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;

class TemplateFieldType extends CustomFieldType
{

    public static function getFieldIdentifier(): string {
        return "template";
    }

    public function viewModes(): array {
        return [
          "default"=> null
        ];
    }

    public function icon(): string {
        return "carbon-copy-file";
    }
}
