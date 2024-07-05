<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\Alignment;

trait HasDragDropItemContainers
{

    protected array $itemContainers = [];

    public function getItemContainers():array
    {
        return  $this->itemContainers;
    }


    public function getItemContainer($key): ComponentContainer
    {
        $this->generateChildContainers();

        $components = $this->getItemContainers();

        if(array_key_exists($key, $components)) return $components[$key];
        $this->generateItemContainer($key);
        return $this->getItemContainers()[$key];
    }

    protected function generateItemContainer(string $key): void {
        $components = $this->getItemSchema($key);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components([Group::make($components)->statePath($key)]); //TODO WHY FILAMENT, WHAT THE FUCK

        $this->itemContainers[$key] = $container;
    }

}
