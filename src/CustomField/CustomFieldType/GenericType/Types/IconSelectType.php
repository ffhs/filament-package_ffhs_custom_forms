<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\IconSelectView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;

class IconSelectType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "icon-select";
    }

    public function viewModes(): array
    {
        return [
            'default' => IconSelectView::class,
        ];
    }

    public function icon(): string
    {
        return "carbon-color-palette";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make(),
        ];
    }


}
