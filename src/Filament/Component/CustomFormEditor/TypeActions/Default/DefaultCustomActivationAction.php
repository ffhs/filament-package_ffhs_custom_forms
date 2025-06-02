<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\FieldTypeAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Set;

class DefaultCustomActivationAction extends FieldTypeAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();

        $this->icon('heroicon-c-sun');
        $this->color(fn(array $fieldData) => ($fieldData['is_active'] ?? false) ? 'success' : 'danger')
            ->tooltip(function (array $fieldData) {
                if ($fieldData['is_active'] ?? false) {
                    return CustomField::__('attributes.is_active.active');
                }
                return CustomField::__('attributes.is_active.not_active');
            });

        $this->action(function (array $fieldData, string $fieldKey, Set $set) {
            $set($fieldKey . '.is_active', !($fieldData['is_active'] ?? false));
        });
    }
}
