<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\CustomEggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views\TabEggTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;

class CustomTabCustomEggType extends CustomEggLayoutType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
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

    public function getExtraTypeOptions(): array{
        return [
            'columns' => new ColumnsOption(),
        ];
    }

}
