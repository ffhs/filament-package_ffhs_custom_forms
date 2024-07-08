<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;


use Filament\Forms\Components\Component;

abstract class FormEditorFieldAdderOld extends Component {
    public static function make(): static {
        $static = app(static::class);
        $static->configure();
        return $static;
    }

}
