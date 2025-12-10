<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

class DefaultCustomActivationAction extends FieldTypeAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->iconButton()
            ->icon(Heroicon::Sun)
            ->color(fn(array $state) => ($state['is_active'] ?? false) ? 'success' : 'danger')
            ->tooltip(function (array $state) {
                if ($state['is_active'] ?? false) {
                    return CustomField::__('attributes.is_active.active');
                }

                return CustomField::__('attributes.is_active.not_active');
            })
            ->action(function (array $state, Set $set) {
                $set('is_active', !($state['is_active'] ?? false));
            });
    }
}
