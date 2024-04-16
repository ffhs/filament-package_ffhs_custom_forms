<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\NestedLayoutType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions\NewEggActionComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Filament\Support\Colors\Color;

abstract class CustomNestLayoutType extends CustomLayoutType
{

    public function repeaterFunctions(): array {
        return array_merge(parent::repeaterFunctions(), [
            NewEggActionComponent::class => NewEggActionComponent::getDefaultTypeClosure($this),
        ]);
    }
    abstract public function getEggType():CustomEggLayoutType;

    public function nameBeforeIconFormEditor(array $state):string {
        $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
        return new HtmlBadge($size, Color::hex('#ab15ab')) . CustomFieldType::nameBeforeIconFormEditor($state);
    }

}
