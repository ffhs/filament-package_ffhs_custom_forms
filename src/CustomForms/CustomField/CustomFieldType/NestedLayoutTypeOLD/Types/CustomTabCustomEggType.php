<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\TabEggTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ColumnsOption;

class CustomTabCustomEggType extends CustomEggLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "tab";
    }

    public function viewModes(): array
    {
        return [
            "default" => TabEggTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "tabler-slideshow";
    }

    public function extraTypeOptions(): array
    {
        return [
            'columns' => ColumnsOption::make(),
        ];
    }

}
