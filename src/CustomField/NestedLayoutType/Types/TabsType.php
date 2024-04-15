<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\NestLayoutType;

class TabsType extends NestLayoutType
{

    public static function getFieldIdentifier(): string {
        return "tabs";
    }

    public function viewModes(): array {
        return  [
          'default'=> "",
        ];
    }

    public function icon(): string {
       return "carbon-new-tab";
    }

    public function getEggType(): EggLayoutType {
        return new TabType();
    }
}
