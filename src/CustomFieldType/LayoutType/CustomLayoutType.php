<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\EditFieldsGroup;

abstract class CustomLayoutType extends CustomFieldType
{
    public function getFieldDataExtraComponents(CustomFormConfiguration $configuration, array $state): array
    {
        return [
            EditFieldsGroup::make('custom_fields')
                ->formConfiguration($configuration)
                ->columnSpanFull()
        ];
    }

    public function fieldEditorExtraComponent(array $rawData): ?string
    {
        return 'filament-package_ffhs_custom_forms::filament.components.drag-drop.container';
    }
}
