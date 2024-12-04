<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;

abstract class CustomLayoutType extends CustomFieldType
{
    public function fieldEditorExtraComponent(array $fieldData): ?string {
        return "filament-package_ffhs_custom_forms::filament.components.drag-drop.container";
    }


}
