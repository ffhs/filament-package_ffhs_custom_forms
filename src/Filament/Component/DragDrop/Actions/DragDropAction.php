<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\HasDragGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;

class DragDropAction extends Action
{
    use HasDragGroup;
    use HasAlignment;
    use HasVerticalAlignment;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.drag-drop.actions.action';

    public function toFormComponent(): ActionContainer|DragDropActionContainer
    {
        $component = DragDropActionContainer::make($this);

        $this->component($component);

        return $component;
    }
}
