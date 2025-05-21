<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\WizardStepEggTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\IconOption;

class WizardStepCustomEggType extends CustomEggLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "wizard_step";
    }

    public function viewModes(): array
    {
        return [
            "default" => WizardStepEggTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "tabler-column-insert-right";
    }

    public function extraTypeOptions(): array
    {
        return [
            'columns' => ColumnsOption::make(),
            'icon' => IconOption::make(),
        ];
    }

    public function hasToolTips(): bool
    {
        return true;
    }

}
