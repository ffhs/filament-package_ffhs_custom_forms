<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\RadioTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutTypeWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;

class RadioType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "radio";
    }

    public function viewModes(): array
    {
        return [
            'default' => RadioTypeView::class,
        ];
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeWithColumnsOptionGroup::make(),
            CustomOptionGroup::make(),
            ValidationTypeOptionGroup::make(),
        ];
    }


    public function icon(): string
    {
        return "carbon-radio-button-checked";
    }
}
