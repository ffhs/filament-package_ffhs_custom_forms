<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;
use Filament\Forms\Components\Component;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;

class DragDropAction extends Action
{
    use HasDragGroup;
    use HasAlignment;
    use HasVerticalAlignment;

    public function toFormComponent(): ActionContainer
    {
        $component = DragDropActionContainer::make($this);

        $this->component($component);

        return $component;
    }


}
