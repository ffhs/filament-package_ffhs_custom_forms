<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\NestLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types\Views\TabsNestTypeView;

class TabsNestType extends NestLayoutType
{
    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "tabs";
    }

    public function viewModes(): array {
        return  [
          'default'=> TabsNestTypeView::class,
        ];
    }

    public function icon(): string {
       return "carbon-new-tab";
    }

    public function getEggType(): EggLayoutType {
        return new TabEggType();
    }
}
