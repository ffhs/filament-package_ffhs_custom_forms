<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;



use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;

abstract class CustomLayoutType extends CustomFieldType
{
    public function canBeRequired(): bool {
        return false;
    }

    public function editModeNameBeforeIcon(array $state):string {
        $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
        $badgeCount = new HtmlBadge($size);
        return $badgeCount . parent::editModeName($state);
    }


}
