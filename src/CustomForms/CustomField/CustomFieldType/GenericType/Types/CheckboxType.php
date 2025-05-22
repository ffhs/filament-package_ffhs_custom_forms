<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views\CheckboxTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;

class CheckboxType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "checkbox";
    }

    public function viewModes(): array
    {
        return [
            'default' => CheckboxTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-check-square";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->removeTypeOption('column_span'),
            ValidationTypeOptionGroup::make(),
        ];
    }


}
