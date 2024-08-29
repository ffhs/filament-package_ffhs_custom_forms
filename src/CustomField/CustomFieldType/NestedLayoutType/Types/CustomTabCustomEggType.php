<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldType\NestedLayoutType\Types\Views\TabEggTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;

class CustomTabCustomEggType extends CustomEggLayoutType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string {
        return "tab";
    }

    public function viewModes(): array {
        return [
            "default"=> TabEggTypeView::class
        ];
    }

    public function icon(): string {
        return "tabler-slideshow";
    }

    public function extraTypeOptions(): array{
        return [
            'columns' => new ColumnsOption(),
        ];
    }

}
