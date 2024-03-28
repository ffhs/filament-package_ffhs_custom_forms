<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;

final class TemplateFieldType extends CustomFieldType
{

    public static function getFieldIdentifier(): string {
        return "template";
    }

    public function viewModes(): array {
        return [
          "default"=> TemplateTypeView::class,
        ];
    }

    public function icon(): string {
        return "carbon-copy-file";
    }
}
