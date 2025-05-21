<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\NestedLayoutTypeOLD;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\OLDRepeaterFieldAction\Actions\NewEggActionComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\html\HtmlBadge;
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
