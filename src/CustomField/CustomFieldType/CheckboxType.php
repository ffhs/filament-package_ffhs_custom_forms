<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\CheckboxTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;

class CheckboxType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string { return "checkbox"; }

    public function viewModes(): array {
        return  [
            'default'  => CheckboxTypeView::class
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'in_line_label' => false,
            'new_line_option' => true,
        ];
    }

    public function getExtraOptionSchema(): ?array {
        return [
            $this->getNewLineOption(),
            $this->getInLineLabelOption(),
        ];
    }
}
