<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullInLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\PullOutLayoutAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Filament\Support\Colors\Color;

abstract class EggLayoutType extends CustomLayoutType
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
