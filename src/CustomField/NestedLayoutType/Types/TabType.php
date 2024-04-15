<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType\EggLayoutType;

class TabType extends EggLayoutType
{

    public static function getFieldIdentifier(): string {
        return "layout_tab";
    }

    public function viewModes(): array {
        return [
          //ToDo
        ];
    }

    public function icon(): string {
        return "tabler-slideshow";
    }
}
