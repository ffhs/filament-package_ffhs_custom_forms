<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Filament\Forms\Components\Actions\Action;

class DefaultCustomActivationAction extends Action
{
    protected function setUp(): void {

        parent::setUp();

        $this->iconButton();
        $this->visible(fn($state, $arguments) =>
            CustomFieldUtils::getFieldTypeFromRawDate($state[$arguments["item"]])->canBeDeactivate()
        );
        $this->icon('heroicon-c-sun');
        $this->color(fn($arguments, $get) => $get($arguments["item"] . ".is_active") ? "success" : "danger")
            ->tooltip(fn($arguments, $get) => $get($arguments["item"] . ".is_active") ? "Aktive" : "Nicht aktiv");

        $this->action(function($get, $arguments, $set) {
            $key = $arguments["item"];
            $fieldState = $get($arguments["item"]);
            $fieldState['is_active'] = !($fieldState['is_active'] ?? false);
            $set('data.'. $key, $fieldState);
        });

    }
}
