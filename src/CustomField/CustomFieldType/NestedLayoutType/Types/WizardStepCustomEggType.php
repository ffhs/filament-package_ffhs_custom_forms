<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\WizardStepEggTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\IconOption;

class WizardStepCustomEggType extends CustomEggLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string {
        return "wizard_step";
    }

    public function viewModes(): array {
        return [
            "default"=> WizardStepEggTypeView::class
        ];
    }

    public function icon(): string {
        return "tabler-column-insert-right";
    }

    public function extraTypeOptions(): array{
        return [
            'columns' => new ColumnsOption(),
            'icon' => new IconOption(),
        ];
    }
    public function hasToolTips(): bool {
        return true;
    }

}
