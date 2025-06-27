<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\Component as FormsComponent;
use Filament\Infolists\Components\Component as InfolistComponent;

trait HasOptionNoComponentModification
{
    public function modifyFormComponent(FormsComponent $component, mixed $value): FormsComponent
    {
        return $component;
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }
}
