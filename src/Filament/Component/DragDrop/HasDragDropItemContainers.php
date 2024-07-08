<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemContainers
{
    public function getItemContainer($key): ComponentContainer
    {
        return $this->getChildComponentContainers()[$key];
    }

    protected function generateItemContainer(string $key): ComponentContainer {
        return ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components($this->getChildComponents())
            ->getClone();
    }

}
