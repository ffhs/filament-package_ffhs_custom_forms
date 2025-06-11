<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;

abstract class CustomLayoutType extends CustomFieldType
{
    public function fieldEditorExtraComponent(array $rawData): ?string
    {
        return "filament-package_ffhs_custom_forms::filament.components.drag-drop.container";
    }
}
