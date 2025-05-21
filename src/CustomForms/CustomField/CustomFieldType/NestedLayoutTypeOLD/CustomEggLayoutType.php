<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\OLDRepeaterFieldAction\Actions\PullInLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\OLDRepeaterFieldAction\Actions\PullOutLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\html\HtmlBadge;
use Filament\Support\Colors\Color;

abstract class CustomEggLayoutType extends CustomLayoutType
{

    public function repeaterFunctions(): array {
        $list = parent::repeaterFunctions();
        unset($list[PullOutLayoutAction::class]);
        unset($list[PullInLayoutAction::class]);
        return $list;
    }

    public function nameBeforeIconFormEditor(array $state):string {
        $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
        return new HtmlBadge($size, Color::hex('#ab15ab')) . CustomFieldType::nameBeforeIconFormEditor($state);
    }


}
