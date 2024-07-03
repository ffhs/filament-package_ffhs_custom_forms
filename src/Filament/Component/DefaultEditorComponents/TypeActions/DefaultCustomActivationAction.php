<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\TypeActions;

use Filament\Forms\Components\Actions\Action;

class DefaultCustomActivationAction extends Action
{


    protected function setUp(): void {

        parent::setUp();

        $this->iconButton();

        $this->icon('heroicon-c-sun');
        $this->color(fn($arguments, $get) => $get($arguments["item"] . ".is_active") ? "success" : "danger")
            ->tooltip(fn($arguments, $get) => $get($arguments["item"] . ".is_active") ? "Aktive" : "Nicht aktiv");

        $this->action(function($get, $arguments, $set) {
            $key = $arguments["item"];
            $set($key . ".is_active", !($get($arguments["item"]. ".is_active")?? false));
        });

    }
}
