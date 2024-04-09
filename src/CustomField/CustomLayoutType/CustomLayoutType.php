<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;



use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;

abstract class CustomLayoutType extends CustomFieldType
{
    public function canBeRequired(): bool {
        return false;
    }

    public function nameBeforeIconFormEditor(array $state):string {
        $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
        return '<span x-on:click.stop="isCollapsed = !isCollapsed" class="cursor-pointer flex" >' .new HtmlBadge($size);
    }

    public function nameFormEditor(array $state):string {
        return parent::nameFormEditor($state) . '</span>';
    }



}
