<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Filament\Forms\ComponentContainer;

trait HasDragDropItemContainers
{
    public function getItemContainer($key): ComponentContainer
    {
        return $this->getChildComponentContainers()[$key];
    }

    protected function generateItemContainer(string $key): ComponentContainer
    {
        return ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components($this->getChildComponents())
            ->getClone();
    }
}
